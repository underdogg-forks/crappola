<?php

namespace App\Http\Controllers;

use App\Ninja\Repositories\DashboardRepository;
use App\Ninja\Transformers\ActivityTransformer;
use Illuminate\Support\Facades\Auth;

class DashboardApiController extends BaseAPIController
{
    public function __construct(DashboardRepository $dashboardRepo)
    {
        parent::__construct();

        $this->dashboardRepo = $dashboardRepo;
    }

    public function index()
    {
        $user = Auth::user();
        $viewAll = $user->hasPermission('view_reports');
        $userId = $user->id;
        $companyId = $user->company->id;
        $defaultCurrency = $user->company->currency_id;

        $dashboardRepo = $this->dashboardRepo;
        $activities = $dashboardRepo->activities($companyId, $userId, $viewAll);

        // optimization for new mobile app
        if (request()->only_activity) {
            return $this->response([
                'id'         => 1,
                'activities' => $this->createCollection($activities, new ActivityTransformer(), ENTITY_ACTIVITY),
            ]);
        }

        $metrics = $dashboardRepo->totals($companyId, $userId, $viewAll);
        $paidToDate = $dashboardRepo->paidToDate($user->company, $userId, $viewAll);
        $averageInvoice = $dashboardRepo->averages($user->company, $userId, $viewAll);
        $balances = $dashboardRepo->balances($user->company, $userId, $viewAll);
        $pastDue = $dashboardRepo->pastDue($companyId, $userId, $viewAll);
        $upcoming = $dashboardRepo->upcoming($companyId, $userId, $viewAll);
        $payments = $dashboardRepo->payments($companyId, $userId, $viewAll);

        $data = [
            'id'                     => 1,
            'paidToDate'             => (float) ($paidToDate->count() && $paidToDate[0]->value ? $paidToDate[0]->value : 0),
            'paidToDateCurrency'     => (int) ($paidToDate->count() && $paidToDate[0]->currency_id ? $paidToDate[0]->currency_id : $defaultCurrency),
            'balances'               => (float) ($balances->count() && $balances[0]->value ? $balances[0]->value : 0),
            'balancesCurrency'       => (int) ($balances->count() && $balances[0]->currency_id ? $balances[0]->currency_id : $defaultCurrency),
            'averageInvoice'         => (float) ($averageInvoice->count() && $averageInvoice[0]->invoice_avg ? $averageInvoice[0]->invoice_avg : 0),
            'averageInvoiceCurrency' => (int) ($averageInvoice->count() && $averageInvoice[0]->currency_id ? $averageInvoice[0]->currency_id : $defaultCurrency),
            'invoicesSent'           => (int) ($metrics ? $metrics->invoices_sent : 0),
            'activeClients'          => (int) ($metrics ? $metrics->active_clients : 0),
            'activities'             => $this->createCollection($activities, new ActivityTransformer(), ENTITY_ACTIVITY),
        ];

        return $this->response($data);
    }
}
