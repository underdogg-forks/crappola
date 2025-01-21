<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Libraries\Utils;
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
use App\Services\RecurringInvoiceService;
use Cache;
use Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

class QuoteController extends BaseController
{
    protected Mailer $mailer;

    protected InvoiceRepository $invoiceRepo;

    protected ClientRepository $clientRepo;

    protected InvoiceService $invoiceService;

    protected $entityType = ENTITY_INVOICE;

    public function __construct(Mailer $mailer, InvoiceRepository $invoiceRepo, ClientRepository $clientRepo, InvoiceService $invoiceService, RecurringInvoiceService $recurringInvoiceService)
    {
        // parent::__construct();

        $this->mailer = $mailer;
        $this->invoiceRepo = $invoiceRepo;
        $this->clientRepo = $clientRepo;
        $this->invoiceService = $invoiceService;
        $this->recurringInvoiceService = $recurringInvoiceService;
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

    public function getRecurringDatatable($clientPublicId = null)
    {
        $companyId = Auth::user()->company_id;
        $search = $request->get('sSearch');

        return $this->recurringInvoiceService->getDatatable($companyId, $clientPublicId, ENTITY_RECURRING_QUOTE, $search);
    }

    public function getDatatable($clientPublicId = null)
    {
        $companyId = Auth::user()->company_id;
        $search = $request->get('sSearch');

        return $this->invoiceService->getDatatable($companyId, $clientPublicId, ENTITY_QUOTE, $search);
    }

    public function create(QuoteRequest $request, $clientPublicId = 0)
    {
        if (! Utils::hasFeature(FEATURE_QUOTES)) {
            return Redirect::to('/invoices/create');
        }

        $company = Auth::user()->company;
        $clientId = null;
        if ($clientPublicId) {
            $clientId = Client::getPrivateId($clientPublicId);
        }
        $invoice = $company->createInvoice(ENTITY_QUOTE, $clientId);
        $invoice->public_id = 0;

        $data = [
            'entityType' => $invoice->getEntityType(),
            'invoice'    => $invoice,
            'data'       => Input::old('data'),
            'method'     => 'POST',
            'url'        => 'invoices',
            'title'      => trans('texts.new_quote'),
        ];
        $data = array_merge($data, self::getViewModel());

        return View::make('invoices.edit', $data);
    }

    /**
     * @return array{entityType: string, company: mixed, products: mixed, taxRateOptions: mixed, clients: mixed, taxRates: mixed, sizes: mixed, paymentTerms: mixed, invoiceDesigns: mixed, invoiceFonts: mixed, invoiceLabels: mixed, isRecurring: false, expenses: Collection}
     */
    private static function getViewModel(): array
    {
        $company = Auth::user()->company;

        return [
            'entityType'     => ENTITY_QUOTE,
            'company'        => Auth::user()->company->load('country'),
            'products'       => Product::scope()->orderBy('product_key')->get(),
            'taxRateOptions' => $company->present()->taxRateOptions,
            'clients'        => Client::scope()->with('contacts', 'country')->orderBy('name')->get(),
            'taxRates'       => TaxRate::scope()->orderBy('name')->get(),
            'sizes'          => Cache::get('sizes'),
            'paymentTerms'   => Cache::get('paymentTerms'),
            'invoiceDesigns' => InvoiceDesign::getDesigns(),
            'invoiceFonts'   => Cache::get('fonts'),
            'invoiceLabels'  => Auth::user()->company->getInvoiceLabels(),
            'isRecurring'    => false,
            'expenses'       => collect(),
        ];
    }

    public function bulk()
    {
        $action = $request->get('bulk_action') ?: $request->get('action');
        $ids = $request->get('bulk_public_id') ?: ($request->get('public_id') ?: $request->get('ids'));

        if ($action == 'convert') {
            $invoice = Invoice::with('invoice_items')->scope($ids)->firstOrFail();
            $clone = $this->invoiceService->convertQuote($invoice);

            Session::flash('message', trans('texts.converted_to_invoice'));

            return Redirect::to('invoices/' . $clone->public_id);
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
            Session::flash('message', $message);
        }

        return $this->returnBulk(ENTITY_QUOTE, $action, $ids);
    }

    public function approve($invitationKey)
    {
        $invitation = Invitation::with('invoice.invoice_items', 'invoice.invitations')->where('invitation_key', '=', $invitationKey)->firstOrFail();
        $invoice = $invitation->invoice;
        $company = $invoice->company;

        if ($company->requiresAuthorization($invoice) && ! session('authorized:' . $invitation->invitation_key)) {
            return redirect()->to('view/' . $invitation->invitation_key);
        }

        if ($invoice->due_at) {
            $carbonDueDate = Carbon::parse($invoice->due_at);
            if (! $company->allow_approve_expired_quote && ! $carbonDueDate->isToday() && ! $carbonDueDate->isFuture()) {
                return redirect("view/{$invitationKey}")->withError(trans('texts.quote_has_expired'));
            }
        }

        if ($invoiceInvitationKey = $this->invoiceService->approveQuote($invoice, $invitation)) {
            Session::flash('message', trans('texts.quote_is_approved'));

            return Redirect::to("view/{$invoiceInvitationKey}");
        }

        return Redirect::to("view/{$invitationKey}");
    }
}
