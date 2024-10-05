<?php

namespace App\Services;

use App\Events\QuoteInvitationWasApproved;
use App\Jobs\DownloadInvoices;
use App\Models\Client;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Ninja\Datatables\InvoiceDatatable;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\InvoiceRepository;
use Utils;

class InvoiceService extends BaseService
{
    protected \App\Ninja\Repositories\ClientRepository $clientRepo;

    protected \App\Ninja\Repositories\InvoiceRepository $invoiceRepo;

    protected \App\Services\DatatableService $datatableService;

    /**
     * InvoiceService constructor.
     *
     * @param ClientRepository  $clientRepo
     * @param InvoiceRepository $invoiceRepo
     * @param DatatableService  $datatableService
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
     * @param $ids
     * @param $action
     *
     * @return int
     */
    public function bulk($ids, $action): int
    {
        if ($action == 'download') {
            $invoices = $this->getRepo()->findByPublicIdsWithTrashed($ids);
            dispatch(new DownloadInvoices(\Illuminate\Support\Facades\Auth::user(), $invoices));

            return count($invoices);
        }

        return parent::bulk($ids, $action);
    }

    /**
     * @param array        $data
     * @param Invoice|null $invoice
     *
     * @return \App\Models\Invoice|Invoice|mixed
     */
    public function save(array $data, ?Invoice $invoice = null)
    {
        if (isset($data['client'])) {
            $canSaveClient = false;
            $canViewClient = false;
            $clientPublicId = \Illuminate\Support\Arr::get($data, 'client.public_id') ?: \Illuminate\Support\Arr::get($data, 'client.id');
            if (empty($clientPublicId) || (int) $clientPublicId < 0) {
                $canSaveClient = \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_CLIENT);
            } else {
                $client = Client::scope($clientPublicId)->first();
                $canSaveClient = \Illuminate\Support\Facades\Auth::user()->can('edit', $client);
                $canViewClient = \Illuminate\Support\Facades\Auth::user()->can('view', $client);
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
     * @param                 $quote
     * @param Invitation|null $invitation
     *
     * @return mixed
     */
    public function convertQuote($quote)
    {
        $account = $quote->account;
        $invoice = $this->invoiceRepo->cloneInvoice($quote, $quote->id);

        if ($account->auto_archive_quote) {
            $this->invoiceRepo->archive($quote);
        }

        return $invoice;
    }

    /**
     * @param                 $quote
     * @param Invitation|null $invitation
     *
     * @return mixed|null
     */
    public function approveQuote($quote, ?Invitation $invitation = null)
    {
        $account = $quote->account;

        if ( ! $account->hasFeature(FEATURE_QUOTES) || ! $quote->isType(INVOICE_TYPE_QUOTE) || $quote->quote_invoice_id) {
            return;
        }

        event(new QuoteInvitationWasApproved($quote, $invitation));

        if ($account->auto_convert_quote) {
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

    public function getDatatable($accountId, $clientPublicId, string $entityType, $search)
    {
        $datatable = new InvoiceDatatable(true, $clientPublicId);
        $datatable->entityType = $entityType;

        $query = $this->invoiceRepo->getInvoices($accountId, $clientPublicId, $entityType, $search)
            ->where('invoices.invoice_type_id', '=', $entityType == ENTITY_QUOTE ? INVOICE_TYPE_QUOTE : INVOICE_TYPE_STANDARD);

        if ( ! Utils::hasPermission('view_' . $entityType)) {
            $query->where('invoices.user_id', '=', \Illuminate\Support\Facades\Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return InvoiceRepository
     */
    protected function getRepo()
    {
        return $this->invoiceRepo;
    }
}
