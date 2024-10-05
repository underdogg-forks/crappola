<?php

namespace App\Services;

use App\Ninja\Datatables\RecurringInvoiceDatatable;
use App\Ninja\Repositories\InvoiceRepository;
use Utils;

class RecurringInvoiceService extends BaseService
{
    protected \App\Ninja\Repositories\InvoiceRepository $invoiceRepo;

    protected \App\Services\DatatableService $datatableService;

    public function __construct(InvoiceRepository $invoiceRepo, DatatableService $datatableService)
    {
        $this->invoiceRepo = $invoiceRepo;
        $this->datatableService = $datatableService;
    }

    public function getDatatable($accountId, $clientPublicId, $entityType, $search)
    {
        $datatable = new RecurringInvoiceDatatable(true, $clientPublicId);
        $query = $this->invoiceRepo->getRecurringInvoices($accountId, $clientPublicId, $search);

        if ( ! Utils::hasPermission('view_recurring_invoice')) {
            $query->where('invoices.user_id', '=', \Illuminate\Support\Facades\Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }
}
