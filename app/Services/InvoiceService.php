<?php

namespace App\Services;

use App\Events\QuoteInvitationWasApproved;
use App\Jobs\DownloadInvoices;
use App\Libraries\Utils;
use App\Models\Client;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Ninja\Datatables\InvoiceDatatable;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\InvoiceRepository;
use Illuminate\Support\Facades\Auth;

class InvoiceService extends BaseService
{
    /**
     * @var ClientRepository
     */
    protected $clientRepo;

    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * InvoiceService constructor.
     */
    public function __construct(
        ClientRepository $clientRepo,
        InvoiceRepository $invoiceRepo,
        DatatableService $datatableService
    ) {
        $this->clientRepo = $clientRepo;
        $this->invoiceRepo = $invoiceRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @return int
     */
    public function bulk($ids, $action)
    {
        if ($action == 'download') {
            $invoices = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            dispatch(new DownloadInvoices(Auth::user(), $invoices));

            return count($invoices);
        }

        return parent::bulk($ids, $action);
    }

    /**
     * @return InvoiceRepository
     */
    protected function getRepo()
    {
        return $this->invoiceRepo;
    }

    /**
     * @return Invoice|Invoice|mixed
     */
    public function save(array $data, Invoice $invoice = null)
    {
        if (isset($data['client'])) {
            $canSaveClient = false;
            $canViewClient = false;
            $clientPublicId = array_get($data, 'client.public_id') ?: array_get($data, 'client.id');
            if (empty($clientPublicId) || intval($clientPublicId) < 0) {
                $canSaveClient = Auth::user()->can('createEntity', ENTITY_CLIENT);
            } else {
                $client = Client::scope($clientPublicId)->first();
                $canSaveClient = Auth::user()->can('edit', $client);
                $canViewClient = Auth::user()->can('view', $client);
            }
            if ($canSaveClient) {
                $client = $this->clientRepo->save($data['client']);
            }
            if ($canSaveClient || $canViewClient) {
                $data['client_id'] = $client->id;
            }
        }

        return $this->invoiceRepo->save($data, $invoice);
    }

    /**
     * @return mixed|null
     */
    public function approveQuote($quote, Invitation $invitation = null)
    {
        $company = $quote->company;

        if (! $company->hasFeature(FEATURE_QUOTES) || ! $quote->isType(INVOICE_TYPE_QUOTE) || $quote->quote_invoice_id) {
            return;
        }

        event(new QuoteInvitationWasApproved($quote, $invitation));

        if ($company->auto_convert_quote) {
            $invoice = $this->convertQuote($quote);

            foreach ($invoice->invitations as $invoiceInvitation) {
                if ($invitation->contact_id == $invoiceInvitation->contact_id) {
                    $invitation = $invoiceInvitation;
                }
            }
        } else {
            $quote->markApproved();
        }

        return $invitation->invitation_key;
    }

    /**
     * @param Invitation|null $invitation
     *
     * @return mixed
     */
    public function convertQuote($quote)
    {
        $company = $quote->company;
        $invoice = $this->invoiceRepo->cloneInvoice($quote, $quote->id);

        if ($company->auto_archive_quote) {
            $this->invoiceRepo->archive($quote);
        }

        return $invoice;
    }

    public function getDatatable($companyId, $clientPublicId, $entityType, $search)
    {
        $datatable = new InvoiceDatatable(true, $clientPublicId);
        $datatable->entityType = $entityType;

        $query = $this->invoiceRepo->getInvoices($companyId, $clientPublicId, $entityType, $search)
            ->where('invoices.invoice_type_id', '=', $entityType == ENTITY_QUOTE ? INVOICE_TYPE_QUOTE : INVOICE_TYPE_STANDARD);

        dd($query->toSql());

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
