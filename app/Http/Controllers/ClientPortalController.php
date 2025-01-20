<?php

namespace App\Http\Controllers;

use App\Events\ClientWasUpdated;
use App\Events\InvoiceInvitationWasViewed;
use App\Events\QuoteInvitationWasViewed;
use App\Jobs\Client\GenerateStatementData;
use App\Libraries\Utils;
use App\Models\Contact;
use App\Models\Document;
use App\Models\PaymentMethod;
use App\Ninja\Repositories\ActivityRepository;
use App\Ninja\Repositories\CreditRepository;
use App\Ninja\Repositories\DocumentRepository;
use App\Ninja\Repositories\InvoiceRepository;
use App\Ninja\Repositories\PaymentRepository;
use App\Ninja\Repositories\TaskRepository;
use App\Services\PaymentService;
use Barracuda\ArchiveStream\ZipArchive;
use Yajra\DataTables\Services\DataTable;
use Exception;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use URL;
use Illuminate\Support\Facades\Validator;

class ClientPortalController extends BaseController
{
    private InvoiceRepository $invoiceRepo;

    private PaymentRepository $paymentRepo;

    private DocumentRepository $documentRepo;

    public function __construct(
        InvoiceRepository $invoiceRepo,
        PaymentRepository $paymentRepo,
        ActivityRepository $activityRepo,
        DocumentRepository $documentRepo,
        PaymentService $paymentService,
        CreditRepository $creditRepo,
        TaskRepository $taskRepo
    ) {
        $this->invoiceRepo = $invoiceRepo;
        $this->paymentRepo = $paymentRepo;
        $this->activityRepo = $activityRepo;
        $this->documentRepo = $documentRepo;
        $this->paymentService = $paymentService;
        $this->creditRepo = $creditRepo;
        $this->taskRepo = $taskRepo;
    }

    public function viewInvoice($invitationKey)
    {
        if (! $invitation = $this->invoiceRepo->findInvoiceByInvitation($invitationKey)) {
            return $this->returnError();
        }

        $invoice = $invitation->invoice;
        $client = $invoice->client;
        $company = $invoice->company;

        if (request()->silent) {
            session(['silent:' . $client->id => true]);

            return redirect(request()->url() . (request()->borderless ? '?borderless=true' : ''));
        }

        if (! $company->checkSubdomain(Request::server('HTTP_HOST'))) {
            return response()->view('error', [
                'error' => trans('texts.invoice_not_found'),
            ]);
        }

        if (! request()->has('phantomjs') && ! session('silent:' . $client->id) && ! Session::has($invitation->invitation_key)
            && (! Auth::check() || Auth::user()->company_id != $invoice->company_id)) {
            if ($invoice->isType(INVOICE_TYPE_QUOTE)) {
                event(new QuoteInvitationWasViewed($invoice, $invitation));
            } else {
                event(new InvoiceInvitationWasViewed($invoice, $invitation));
            }
        }

        Session::put($invitation->invitation_key, true); // track this invitation has been seen
        Session::put('contact_key', $invitation->contact->contact_key); // track current contact

        $invoice->invoice_date = Utils::fromSqlDate($invoice->invoice_date);
        $invoice->due_at = Utils::fromSqlDate($invoice->due_at);
        $invoice->partial_due_date = Utils::fromSqlDate($invoice->partial_due_date);
        $invoice->features = [
            'customize_invoice_design' => $company->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN),
            'remove_created_by'        => $company->hasFeature(FEATURE_REMOVE_CREATED_BY),
            'invoice_settings'         => $company->hasFeature(FEATURE_INVOICE_SETTINGS),
        ];
        $invoice->invoice_fonts = $company->getFontsData();

        if ($design = $company->getCustomDesign($invoice->invoice_design_id)) {
            $invoice->invoice_design->javascript = $design;
        } else {
            $invoice->invoice_design->javascript = $invoice->invoice_design->pdfmake;
        }
        $contact = $invitation->contact;
        $contact->setVisible([
            'first_name',
            'last_name',
            'email',
            'phone',
            'custom_value1',
            'custom_value2',
        ]);
        $company->load(['date_format', 'datetime_format']);

        // translate the country names
        if ($invoice->client->country) {
            $invoice->client->country->name = $invoice->client->country->getName();
        }
        if ($invoice->company->country) {
            $invoice->company->country->name = $invoice->company->country->getName();
        }

        $data = [];
        $paymentTypes = $this->getPaymentTypes($company, $client, $invitation);
        $paymentURL = '';
        if (count($paymentTypes) == 1) {
            $paymentURL = $paymentTypes[0]['url'];
            if (in_array($paymentTypes[0]['gatewayTypeId'], [GATEWAY_TYPE_CUSTOM1, GATEWAY_TYPE_CUSTOM2, GATEWAY_TYPE_CUSTOM3])) {
                // do nothing
            } elseif (! $company->isGatewayConfigured(GATEWAY_PAYPAL_EXPRESS)) {
                $paymentURL = URL::to($paymentURL);
            }
        }

        if (! request()->has('phantomjs')) {
            if ($wepayGateway = $company->getGatewayConfig(GATEWAY_WEPAY)) {
                $data['enableWePayACH'] = $wepayGateway->getAchEnabled();
            }
            if ($stripeGateway = $company->getGatewayConfig(GATEWAY_STRIPE)) {
                //$data['enableStripeSources'] = $stripeGateway->getAlipayEnabled();
                $data['enableStripeSources'] = true;
            }
        }

        $showApprove = ($invoice->isQuote() && $company->require_approve_quote) ? true : false;
        if ($invoice->invoice_status_id >= INVOICE_STATUS_APPROVED) {
            $showApprove = false;
        }

        $data += [
            'company'         => $company,
            'approveRequired' => $company->require_approve_quote,
            'showApprove'     => $showApprove,
            'showBreadcrumbs' => false,
            'invoice'         => $invoice->hidePrivateFields(),
            'invitation'      => $invitation,
            'invoiceLabels'   => $company->getInvoiceLabels(),
            'contact'         => $contact,
            'paymentTypes'    => $paymentTypes,
            'paymentURL'      => $paymentURL,
            'phantomjs'       => request()->has('phantomjs'),
            'gatewayTypeId'   => count($paymentTypes) == 1 ? $paymentTypes[0]['gatewayTypeId'] : false,
        ];

        if ($invoice->canBePaid()) {
            if ($paymentDriver = $company->paymentDriver($invitation, GATEWAY_TYPE_CREDIT_CARD)) {
                $data += [
                    'transactionToken' => $paymentDriver->createTransactionToken(),
                    'partialView'      => $paymentDriver->partialView(),
                    'accountGateway'   => $paymentDriver->accountGateway,
                ];
            }
        }

        if ($company->hasFeature(FEATURE_DOCUMENTS) && $this->canCreateZip()) {
            $zipDocs = $this->getInvoiceZipDocuments($invoice, $size);

            if (count($zipDocs) > 1) {
                $data['documentsZipURL'] = URL::to("client/documents/{$invitation->invitation_key}");
                $data['documentsZipSize'] = $size;
            }
        }

        return View::make(request()->borderless ? 'invoices.view_borderless' : 'invoices.view', $data);
    }

    public function returnError($error = false)
    {
        if (request()->phantomjs) {
            abort(404);
        }

        return response()->view('error', [
            'error'      => $error ?: trans('texts.invoice_not_found'),
            'hideHeader' => true,
            'company'    => $this->getContact() ? $this->getContact()->company : false,
        ]);
    }

    public function getContact()
    {
        $contactKey = session('contact_key');

        if (! $contactKey) {
            return false;
        }

        $contact = Contact::where('contact_key', '=', $contactKey)->first();
        if (! $contact) {
            return false;
        }
        if ($contact->is_deleted) {
            return false;
        }

        return $contact;
    }

    private function getPaymentTypes($company, $client, $invitation)
    {
        $links = [];

        foreach ($company->account_gateways as $companyGateway) {
            $paymentDriver = $companyGateway->paymentDriver($invitation);
            $links = array_merge($links, $paymentDriver->tokenLinks());
            $links = array_merge($links, $paymentDriver->paymentLinks());
        }

        return $links;
    }

    protected function canCreateZip(): bool
    {
        return function_exists('gmp_init');
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function getInvoiceZipDocuments($invoice, &$size = 0): array
    {
        $documents = $invoice->documents;

        foreach ($invoice->expenses as $expense) {
            if ($expense->invoice_documents) {
                $documents = $documents->merge($expense->documents);
            }
        }

        $documents = $documents->sortBy('size');

        $size = 0;
        $maxSize = MAX_ZIP_DOCUMENTS_SIZE * 1000;
        $toZip = [];
        foreach ($documents as $document) {
            if ($size + $document->size > $maxSize) {
                break;
            }

            if (! empty($toZip[$document->name])) {
                // This name is taken
                if ($toZip[$document->name]->hash != $document->hash) {
                    // 2 different files with the same name
                    $nameInfo = pathinfo($document->name);

                    for ($i = 1; ; $i++) {
                        $name = $nameInfo['filename'] . ' (' . $i . ').' . $nameInfo['extension'];

                        if (empty($toZip[$name])) {
                            $toZip[$name] = $document;
                            $size += $document->size;
                            break;
                        } elseif ($toZip[$name]->hash == $document->hash) {
                            // We're not adding this after all
                            break;
                        }
                    }
                }
            } else {
                $toZip[$document->name] = $document;
                $size += $document->size;
            }
        }

        return $toZip;
    }

    public function download($invitationKey)
    {
        if (! $invitation = $this->invoiceRepo->findInvoiceByInvitation($invitationKey)) {
            return response()->view('error', [
                'error'      => trans('texts.invoice_not_found'),
                'hideHeader' => true,
            ]);
        }

        $invoice = $invitation->invoice;
        $decode = ! request()->base64;
        $pdfString = $invoice->getPDFString($invitation, $decode);

        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($pdfString));
        header('Content-disposition: attachment; filename="' . $invoice->getFileName() . '"');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        return $pdfString;
    }

    public function authorizeInvoice($invitationKey)
    {
        if (! $invitation = $this->invoiceRepo->findInvoiceByInvitation($invitationKey)) {
            return RESULT_FAILURE;
        }

        if ($signature = $request->get('signature')) {
            $invitation->signature_base64 = $signature;
            $invitation->signature_date = date_create();
            $invitation->save();
        }

        session(['authorized:' . $invitation->invitation_key => true]);

        return RESULT_SUCCESS;
    }

    public function dashboard($contactKey = false)
    {
        if ($contactKey) {
            if (! $contact = Contact::where('contact_key', '=', $contactKey)->first()) {
                return $this->returnError();
            }
            Session::put('contact_key', $contactKey); // track current contact
        } elseif (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $client = $contact->client;
        $company = $client->company;

        if (request()->silent) {
            session(['silent:' . $client->id => true]);

            return redirect(request()->url());
        }

        $color = $company->primary_color ? $company->primary_color : '#0b4d78';
        $customer = false;
        if (! $company->enable_client_portal) {
            return $this->returnError();
        }

        if (! $company->enable_client_portal_dashboard) {
            session()->reflash();

            return redirect()->to('/client/invoices/');
        }

        if ($paymentDriver = $company->paymentDriver(false, GATEWAY_TYPE_TOKEN)) {
            $customer = $paymentDriver->customer($client->id);
        }

        $data = [
            'color'            => $color,
            'contact'          => $contact,
            'company'          => $company,
            'client'           => $client,
            'gateway'          => $company->getTokenGateway(),
            'paymentMethods'   => $customer ? $customer->payment_methods : false,
            'transactionToken' => $paymentDriver ? $paymentDriver->createTransactionToken() : false,
        ];

        return response()->view('invited.dashboard', $data);
    }

    public function activityDatatable()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $client = $contact->client;

        $query = $this->activityRepo->findByClientId($client->id);
        $query->where('activities.adjustment', '!=', 0);

        return Datatable::query($query)
            ->addColumn('activities.id', function ($model) {
                return Utils::timestampToDateTimeString(strtotime($model->created_at));
            })
            ->addColumn('activity_type_id', function ($model): array|Translator|string|null {
                $data = [
                    'client'         => Utils::getClientDisplayName($model),
                    'user'           => $model->is_system ? ('<i>' . trans('texts.system') . '</i>') : ($model->company_name),
                    'invoice'        => $model->invoice,
                    'contact'        => Utils::getClientDisplayName($model),
                    'payment'        => $model->payment ? ' ' . $model->payment : '',
                    'credit'         => $model->payment_amount ? Utils::formatMoney($model->credit, $model->currency_id, $model->country_id) : '',
                    'payment_amount' => $model->payment_amount ? Utils::formatMoney($model->payment_amount, $model->currency_id, $model->country_id) : null,
                    'adjustment'     => $model->adjustment ? Utils::formatMoney($model->adjustment, $model->currency_id, $model->country_id) : null,
                ];

                return trans("texts.activity_{$model->activity_type_id}", $data);
            })
            ->addColumn('balance', function ($model) {
                return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
            })
            ->addColumn('adjustment', function ($model) {
                return $model->adjustment != 0 ? Utils::wrapAdjustment($model->adjustment, $model->currency_id, $model->country_id) : '';
            })
            ->make();
    }

    public function recurringQuoteIndex()
    {
        return self::recurringInvoiceIndex(true);
    }

    public function recurringInvoiceIndex($quotes = false)
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $company = $contact->company;

        if (! $company->enable_client_portal) {
            return $this->returnError();
        }

        $color = $company->primary_color ? $company->primary_color : '#0b4d78';
        $columns = ['frequency', 'start_date', 'end_date', 'invoice_total'];
        $client = $contact->client;

        if ($client->hasAutoBillConfigurableInvoices()) {
            $columns[] = 'auto_bill';
        }

        $title = trans('texts.recurring_invoices');
        $entityType = ENTITY_RECURRING_INVOICE;
        if ($quotes) {
            $title = trans('texts.recurring_quotes');
            $entityType = ENTITY_RECURRING_QUOTE;
        }

        $data = [
            'color'      => $color,
            'company'    => $company,
            'client'     => $client,
            'title'      => $title,
            'entityType' => $entityType,
            'columns'    => Utils::trans($columns),
            'sortColumn' => 1,
        ];

        return response()->view('public_list', $data);
    }

    public function invoiceIndex()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $company = $contact->company;

        if (! $company->enable_client_portal) {
            return $this->returnError();
        }

        $color = $company->primary_color ? $company->primary_color : '#0b4d78';

        $data = [
            'color'      => $color,
            'company'    => $company,
            'client'     => $contact->client,
            'title'      => trans('texts.invoices'),
            'entityType' => ENTITY_INVOICE,
            'columns'    => Utils::trans(['invoice_number', 'invoice_date', 'invoice_total', 'balance_due', 'due_at', 'status']),
            'sortColumn' => 1,
        ];

        return response()->view('public_list', $data);
    }

    public function invoiceDatatable()
    {
        if (! $contact = $this->getContact()) {
            return '';
        }

        return $this->invoiceRepo->getClientDatatable($contact->id, ENTITY_INVOICE, $request->get('sSearch'));
    }

    public function recurringInvoiceDatatable()
    {
        if (! $contact = $this->getContact()) {
            return '';
        }

        return $this->invoiceRepo->getClientRecurringDatatable($contact->id);
    }

    public function recurringQuoteDatatable()
    {
        if (! $contact = $this->getContact()) {
            return '';
        }

        return $this->invoiceRepo->getClientRecurringDatatable($contact->id, ENTITY_RECURRING_QUOTE);
    }

    public function paymentIndex()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $company = $contact->company;

        if (! $company->enable_client_portal) {
            return $this->returnError();
        }

        $color = $company->primary_color ? $company->primary_color : '#0b4d78';

        $data = [
            'color'      => $color,
            'company'    => $company,
            'entityType' => ENTITY_PAYMENT,
            'title'      => trans('texts.payments'),
            'columns'    => Utils::trans(['invoice', 'transaction_reference', 'method', 'payment_amount', 'payment_date', 'status']),
            'sortColumn' => 4,
        ];

        return response()->view('public_list', $data);
    }

    public function paymentDatatable()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }
        $payments = $this->paymentRepo->findForContact($contact->id, $request->get('sSearch'));

        return Datatable::query($payments)
            ->addColumn('invoice_number', function ($model) {
                return $model->invitation_key ? link_to('/view/' . $model->invitation_key, $model->invoice_number)->toHtml() : $model->invoice_number;
            })
            ->addColumn('transaction_reference', function ($model): string {
                return $model->transaction_reference ? e($model->transaction_reference) : '<i>' . trans('texts.manual_entry') . '</i>';
            })
            ->addColumn('payment_type', function ($model) {
                return ($model->payment_type && ! $model->last4) ? $model->payment_type : ($model->account_gateway_id ? '<i>Online payment</i>' : '');
            })
            ->addColumn('amount', function ($model) {
                return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id);
            })
            ->addColumn('payment_date', function ($model) {
                return Utils::dateToString($model->payment_date);
            })
            ->addColumn('status', function ($model) {
                return $this->getPaymentStatusLabel($model);
            })
            ->orderColumns('invoice_number', 'transaction_reference', 'payment_type', 'amount', 'payment_date')
            ->make();
    }

    private function getPaymentStatusLabel($model)
    {
        $label = trans('texts.status_' . strtolower($model->payment_status_name));
        $class = 'default';
        switch ($model->payment_status_id) {
            case PAYMENT_STATUS_PENDING:
                $class = 'info';
                break;
            case PAYMENT_STATUS_COMPLETED:
                $class = 'success';
                break;
            case PAYMENT_STATUS_FAILED:
                $class = 'danger';
                break;
            case PAYMENT_STATUS_PARTIALLY_REFUNDED:
                $label = trans('texts.status_partially_refunded_amount', [
                    'amount' => Utils::formatMoney($model->refunded, $model->currency_id, $model->country_id),
                ]);
                $class = 'primary';
                break;
            case PAYMENT_STATUS_REFUNDED:
                $class = 'default';
                break;
        }

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }

    public function quoteIndex()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $company = $contact->company;

        if (! $company->enable_client_portal) {
            return $this->returnError();
        }

        $color = $company->primary_color ? $company->primary_color : '#0b4d78';

        $data = [
            'color'      => $color,
            'company'    => $company,
            'client'     => $contact->client,
            'title'      => trans('texts.quotes'),
            'entityType' => ENTITY_QUOTE,
            'columns'    => Utils::trans(['quote_number', 'quote_date', 'quote_total', 'due_at', 'status']),
            'sortColumn' => 1,
        ];

        return response()->view('public_list', $data);
    }

    public function quoteDatatable()
    {
        if (! $contact = $this->getContact()) {
            return false;
        }

        return $this->invoiceRepo->getClientDatatable($contact->id, ENTITY_QUOTE, $request->get('sSearch'));
    }

    public function creditIndex()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $company = $contact->company;

        if (! $company->enable_client_portal) {
            return $this->returnError();
        }

        $color = $company->primary_color ? $company->primary_color : '#0b4d78';

        $data = [
            'color'      => $color,
            'company'    => $company,
            'title'      => trans('texts.credits'),
            'entityType' => ENTITY_CREDIT,
            'columns'    => Utils::trans(['credit_date', 'credit_amount', 'credit_balance', 'notes']),
            'sortColumn' => 0,
        ];

        return response()->view('public_list', $data);
    }

    public function creditDatatable()
    {
        if (! $contact = $this->getContact()) {
            return false;
        }

        return $this->creditRepo->getClientDatatable($contact->client_id);
    }

    public function taskIndex()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $company = $contact->company;

        if (! $contact->client->show_tasks_in_portal) {
            return redirect()->to($company->enable_client_portal_dashboard ? '/client/dashboard' : '/client/payment_methods/');
        }

        if (! $company->enable_client_portal) {
            return $this->returnError();
        }

        $color = $company->primary_color ? $company->primary_color : '#0b4d78';

        $data = [
            'color'      => $color,
            'company'    => $company,
            'title'      => trans('texts.tasks'),
            'entityType' => ENTITY_TASK,
            'columns'    => Utils::trans(['project', 'date', 'duration', 'description']),
            'sortColumn' => 1,
        ];

        return response()->view('public_list', $data);
    }

    public function taskDatatable()
    {
        if (! $contact = $this->getContact()) {
            return false;
        }

        return $this->taskRepo->getClientDatatable($contact->client_id);
    }

    public function documentIndex()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $company = $contact->company;

        if (! $company->enable_client_portal) {
            return $this->returnError();
        }

        $color = $company->primary_color ? $company->primary_color : '#0b4d78';

        $data = [
            'color'      => $color,
            'company'    => $company,
            'title'      => trans('texts.documents'),
            'entityType' => ENTITY_DOCUMENT,
            'columns'    => Utils::trans(['invoice_number', 'name', 'document_date', 'document_size']),
            'sortColumn' => 2,
        ];

        return response()->view('public_list', $data);
    }

    public function documentDatatable()
    {
        if (! $contact = $this->getContact()) {
            return false;
        }

        return $this->documentRepo->getClientDatatable($contact->id, ENTITY_DOCUMENT, $request->get('sSearch'));
    }

    public function getDocumentVFSJS($publicId, $name)
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $document = Document::scope($publicId, $contact->company_id)->first();

        if (! $document->isPDFEmbeddable()) {
            return Response::view('error', ['error' => 'Image does not exist!'], 404);
        }

        $authorized = false;
        if ($document->expense && $document->expense->client_id == $contact->client_id) {
            $authorized = true;
        } elseif ($document->invoice && $document->invoice->client_id == $contact->client_id) {
            $authorized = true;
        }

        if (! $authorized) {
            return Response::view('error', ['error' => 'Not authorized'], 403);
        }

        if (substr($name, -3) == '.js') {
            $name = substr($name, 0, -3);
        }

        $content = $document->preview ? $document->getRawPreview() : $document->getRaw();
        $content = 'ninjaAddVFSDoc(' . json_encode(intval($publicId) . '/' . strval($name)) . ',"' . base64_encode($content) . '")';
        $response = Response::make($content, 200);
        $response->header('content-type', 'text/javascript');
        $response->header('cache-control', 'max-age=31536000');

        return $response;
    }

    public function getInvoiceDocumentsZip($invitationKey)
    {
        if (! $invitation = $this->invoiceRepo->findInvoiceByInvitation($invitationKey)) {
            return $this->returnError();
        }

        Session::put('contact_key', $invitation->contact->contact_key); // track current contact

        $invoice = $invitation->invoice;

        $toZip = $this->getInvoiceZipDocuments($invoice);

        if (! count($toZip)) {
            return Response::view('error', ['error' => 'No documents small enough'], 404);
        }

        $zip = new ZipArchive($invitation->company->name . ' Invoice ' . $invoice->invoice_number . '.zip');

        return Response::stream(function () use ($toZip, $zip): void {
            foreach ($toZip as $name => $document) {
                $fileStream = $document->getStream();
                if ($fileStream) {
                    $zip->init_file_stream_transfer($name, $document->size, ['time' => $document->created_at->timestamp]);
                    while ($buffer = fread($fileStream, 256000)) {
                        $zip->stream_file_part($buffer);
                    }
                    fclose($fileStream);
                    $zip->complete_file_stream();
                } else {
                    $zip->add_file($name, $document->getRaw());
                }
            }
            $zip->finish();
        }, 200);
    }

    public function getDocument($invitationKey, $publicId)
    {
        if (! $invitation = $this->invoiceRepo->findInvoiceByInvitation($invitationKey)) {
            return $this->returnError();
        }

        Session::put('contact_key', $invitation->contact->contact_key); // track current contact

        $clientId = $invitation->invoice->client_id;
        $document = Document::scope($publicId, $invitation->company_id)->firstOrFail();

        $authorized = false;
        if ($document->is_default) {
            $authorized = true;
        } elseif ($document->expense && $document->expense->invoice_documents && $document->expense->client_id == $invitation->invoice->client_id) {
            $authorized = true;
        } elseif ($document->invoice && $document->invoice->client_id == $invitation->invoice->client_id) {
            $authorized = true;
        }

        if (! $authorized) {
            return Response::view('error', ['error' => 'Not authorized'], 403);
        }

        return DocumentController::getDownloadResponse($document);
    }

    public function paymentMethods()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $client = $contact->client;
        $company = $client->company;

        $paymentDriver = $company->paymentDriver(false, GATEWAY_TYPE_TOKEN);
        $customer = $paymentDriver->customer($client->id);

        $data = [
            'company'          => $company,
            'contact'          => $contact,
            'color'            => $company->primary_color ? $company->primary_color : '#0b4d78',
            'client'           => $client,
            'paymentMethods'   => $customer ? $customer->payment_methods : false,
            'gateway'          => $company->getTokenGateway(),
            'title'            => trans('texts.payment_methods'),
            'transactionToken' => $paymentDriver->createTransactionToken(),
        ];

        return response()->view('payments.paymentmethods', $data);
    }

    public function verifyPaymentMethod()
    {
        $publicId = $request->get('source_id');
        $amount1 = $request->get('verification1');
        $amount2 = $request->get('verification2');

        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $client = $contact->client;
        $company = $client->company;

        $paymentDriver = $company->paymentDriver(null, GATEWAY_TYPE_BANK_TRANSFER);
        $result = $paymentDriver->verifyBankAccount($client, $publicId, $amount1, $amount2);

        if (is_string($result)) {
            Session::flash('error', $result);
        } else {
            Session::flash('message', trans('texts.payment_method_verified'));
        }

        return redirect()->to($company->enable_client_portal_dashboard ? '/client/dashboard' : '/client/payment_methods/');
    }

    public function removePaymentMethod($publicId)
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $client = $contact->client;
        $company = $contact->company;

        $paymentDriver = $company->paymentDriver(false, GATEWAY_TYPE_TOKEN);
        $paymentMethod = PaymentMethod::clientId($client->id)
            ->wherePublicId($publicId)
            ->firstOrFail();

        try {
            $paymentDriver->removePaymentMethod($paymentMethod);
            Session::flash('message', trans('texts.payment_method_removed'));
        } catch (Exception $exception) {
            Session::flash('error', $exception->getMessage());
        }

        return redirect()->to($client->company->enable_client_portal_dashboard ? '/client/dashboard' : '/client/payment_methods/');
    }

    public function setDefaultPaymentMethod()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $client = $contact->client;
        $company = $client->company;

        $validator = Validator::make(\Request::all(), ['source' => 'required']);
        if ($validator->fails()) {
            return Redirect::to($client->company->enable_client_portal_dashboard ? '/client/dashboard' : '/client/payment_methods/');
        }

        $paymentDriver = $company->paymentDriver(false, GATEWAY_TYPE_TOKEN);
        $paymentMethod = PaymentMethod::clientId($client->id)
            ->wherePublicId($request->get('source'))
            ->firstOrFail();

        $customer = $paymentDriver->customer($client->id);
        $customer->default_payment_method_id = $paymentMethod->id;
        $customer->save();

        Session::flash('message', trans('texts.payment_method_set_as_default'));

        return redirect()->to($client->company->enable_client_portal_dashboard ? '/client/dashboard' : '/client/payment_methods/');
    }

    public function setAutoBill()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $client = $contact->client;

        $validator = Validator::make(\Request::all(), ['public_id' => 'required']);

        if ($validator->fails()) {
            return Redirect::to('client/invoices/recurring');
        }

        $publicId = $request->get('public_id');
        $enable = $request->get('enable');
        $invoice = $client->invoices()->where('public_id', intval($publicId))->first();

        if ($invoice && $invoice->is_recurring && ($invoice->auto_bill == AUTO_BILL_OPT_IN || $invoice->auto_bill == AUTO_BILL_OPT_OUT)) {
            $invoice->client_enable_auto_bill = $enable ? true : false;
            $invoice->save();
        }

        return Redirect::to('client/invoices/recurring');
    }

    public function showDetails()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $data = [
            'contact' => $contact,
            'client'  => $contact->client,
            'company' => $contact->company,
        ];

        return view('invited.details', $data);
    }

    public function updateDetails(Request $request)
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $client = $contact->client;
        $company = $contact->company;

        if (! $company->enable_client_portal) {
            return $this->returnError();
        }

        $rules = [
            'email'       => 'required',
            'address1'    => 'required',
            'city'        => 'required',
            'state'       => $company->requiresAddressState() ? 'required' : '',
            'postal_code' => 'required',
            'country_id'  => 'required',
        ];

        if ($client->name) {
            $rules['name'] = 'required';
        } else {
            $rules['first_name'] = 'required';
            $rules['last_name'] = 'required';
        }
        if ($company->vat_number || $company->isNinjaAccount()) {
            $rules['vat_number'] = 'required';
        }

        $this->validate($request, $rules);

        $contact->fill(request()->all());
        $contact->save();

        $client->fill(request()->all());
        $client->save();

        event(new ClientWasUpdated($client));

        return redirect($company->enable_client_portal_dashboard ? '/client/dashboard' : '/client/payment_methods')
            ->withMessage(trans('texts.updated_client_details'));
    }

    public function statement()
    {
        if (! $contact = $this->getContact()) {
            return $this->returnError();
        }

        $client = $contact->client;
        $company = $contact->company;
        if (! $company->enable_client_portal) {
            return $this->returnError();
        }
        if (! $company->enable_client_portal_dashboard) {
            return $this->returnError();
        }

        $statusId = request()->status_id;
        $startDate = request()->start_date;
        $endDate = request()->end_date;

        if (! $startDate) {
            $startDate = Utils::today(false)->modify('-6 month')->format('Y-m-d');
            $endDate = Utils::today(false)->format('Y-m-d');
        }

        if (request()->json) {
            return dispatch_now(new GenerateStatementData($client, request()->all(), $contact));
        }

        $data = [
            'extends'   => 'public.header',
            'client'    => $client,
            'company'   => $company,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ];

        return view('clients.statement', $data);
    }

    private function paymentMethodError($type, $error, $companyGateway = false, $exception = false): void
    {
        $message = '';
        if ($companyGateway && $companyGateway->gateway) {
            $message = $companyGateway->gateway->name . ': ';
        }
        $message .= $error ?: trans('texts.payment_method_error');

        Session::flash('error', $message);
        Utils::logError("Payment Method Error [{$type}]: " . ($exception ? Utils::getErrorString($exception) : $message), 'PHP', true);
    }
}
