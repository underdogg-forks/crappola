<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Models\Client;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Models\InvoiceDesign;
use App\Models\Product;
use App\Models\TaxRate;
use App\Ninja\Datatables\InvoiceDatatable;
use App\Ninja\Mailers\ContactMailer as Mailer;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\InvoiceRepository;
use App\Services\InvoiceService;
use Carbon;
use Utils;

class QuoteController extends BaseController
{
    protected Mailer $mailer;

    protected \App\Ninja\Repositories\InvoiceRepository $invoiceRepo;

    protected \App\Ninja\Repositories\ClientRepository $clientRepo;

    protected \App\Services\InvoiceService $invoiceService;

    protected $entityType = ENTITY_INVOICE;

    public function __construct(Mailer $mailer, InvoiceRepository $invoiceRepo, ClientRepository $clientRepo, InvoiceService $invoiceService)
    {
        // parent::__construct();

        $this->mailer = $mailer;
        $this->invoiceRepo = $invoiceRepo;
        $this->clientRepo = $clientRepo;
        $this->invoiceService = $invoiceService;
    }

    public function index()
    {
        $datatable = new InvoiceDatatable();
        $datatable->entityType = ENTITY_QUOTE;

        $data = [
            'title'      => trans('texts.quotes'),
            'entityType' => ENTITY_QUOTE,
            'datatable'  => $datatable,
        ];

        return response()->view('list_wrapper', $data);
    }

    public function getDatatable($clientPublicId = null)
    {
        $accountId = \Illuminate\Support\Facades\Auth::user()->account_id;
        $search = \Illuminate\Support\Facades\Request::input('sSearch');

        return $this->invoiceService->getDatatable($accountId, $clientPublicId, ENTITY_QUOTE, $search);
    }

    public function create(QuoteRequest $request, $clientPublicId = 0)
    {
        if ( ! Utils::hasFeature(FEATURE_QUOTES)) {
            return \Illuminate\Support\Facades\Redirect::to('/invoices/create');
        }

        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $clientId = null;
        if ($clientPublicId) {
            $clientId = Client::getPrivateId($clientPublicId);
        }
        $invoice = $account->createInvoice(ENTITY_QUOTE, $clientId);
        $invoice->public_id = 0;

        $data = [
            'entityType' => $invoice->getEntityType(),
            'invoice'    => $invoice,
            'data'       => \Illuminate\Support\Facades\Request::old('data'),
            'method'     => 'POST',
            'url'        => 'invoices',
            'title'      => trans('texts.new_quote'),
        ];
        $data = array_merge($data, $this->getViewModel());

        return \Illuminate\Support\Facades\View::make('invoices.edit', $data);
    }

    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('bulk_action') ?: \Illuminate\Support\Facades\Request::input('action');

        $ids = \Illuminate\Support\Facades\Request::input('bulk_public_id') ?: (\Illuminate\Support\Facades\Request::input('public_id') ?: \Illuminate\Support\Facades\Request::input('ids'));

        if ($action == 'convert') {
            $invoice = Invoice::with('invoice_items')->scope($ids)->firstOrFail();
            $clone = $this->invoiceService->convertQuote($invoice);

            \Illuminate\Support\Facades\Session::flash('message', trans('texts.converted_to_invoice'));

            return \Illuminate\Support\Facades\Redirect::to('invoices/' . $clone->public_id);
        }

        $count = $this->invoiceService->bulk($ids, $action);

        if ($count > 0) {
            if ($action == 'markSent') {
                $key = 'updated_quote';
            } elseif ($action == 'download') {
                $key = 'downloaded_quote';
            } else {
                $key = "{$action}d_quote";
            }
            $message = Utils::pluralize($key, $count);
            \Illuminate\Support\Facades\Session::flash('message', $message);
        }

        return $this->returnBulk(ENTITY_QUOTE, $action, $ids);
    }

    public function approve($invitationKey)
    {
        $invitation = Invitation::with('invoice.invoice_items', 'invoice.invitations')->where('invitation_key', '=', $invitationKey)->firstOrFail();
        $invoice = $invitation->invoice;
        $account = $invoice->account;

        if ($account->requiresAuthorization($invoice) && ! session('authorized:' . $invitation->invitation_key)) {
            return redirect()->to('view/' . $invitation->invitation_key);
        }

        if ($invoice->due_date) {
            $carbonDueDate = Carbon::parse($invoice->due_date);
            if ( ! $carbonDueDate->isToday() && ! $carbonDueDate->isFuture()) {
                return redirect("view/{$invitationKey}")->withError(trans('texts.quote_has_expired'));
            }
        }

        if ($invoiceInvitationKey = $this->invoiceService->approveQuote($invoice, $invitation)) {
            \Illuminate\Support\Facades\Session::flash('message', trans('texts.quote_is_approved'));

            return \Illuminate\Support\Facades\Redirect::to("view/{$invoiceInvitationKey}");
        }

        return \Illuminate\Support\Facades\Redirect::to("view/{$invitationKey}");
    }

    private function getViewModel(): array
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;

        return [
            'entityType'     => ENTITY_QUOTE,
            'account'        => \Illuminate\Support\Facades\Auth::user()->account->load('country'),
            'products'       => Product::scope()->orderBy('product_key')->get(),
            'taxRateOptions' => $account->present()->taxRateOptions,
            'clients'        => Client::scope()->with('contacts', 'country')->orderBy('name')->get(),
            'taxRates'       => TaxRate::scope()->orderBy('name')->get(),
            'sizes'          => \Illuminate\Support\Facades\Cache::get('sizes'),
            'paymentTerms'   => \Illuminate\Support\Facades\Cache::get('paymentTerms'),
            'invoiceDesigns' => InvoiceDesign::getDesigns(),
            'invoiceFonts'   => \Illuminate\Support\Facades\Cache::get('fonts'),
            'invoiceLabels'  => \Illuminate\Support\Facades\Auth::user()->account->getInvoiceLabels(),
            'isRecurring'    => false,
            'expenses'       => collect(),
        ];
    }
}
