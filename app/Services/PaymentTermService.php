<?php

namespace App\Services;

use App\Ninja\Datatables\PaymentTermDatatable;
use App\Ninja\Repositories\PaymentTermRepository;

class PaymentTermService extends BaseService
{
    protected \App\Ninja\Repositories\PaymentTermRepository $paymentTermRepo;

    protected \App\Services\DatatableService $datatableService;

    /**
     * PaymentTermService constructor.
     *
     * @param PaymentTermRepository $paymentTermRepo
     * @param DatatableService      $datatableService
     */
    public function __construct(PaymentTermRepository $paymentTermRepo, DatatableService $datatableService)
    {
        $this->paymentTermRepo = $paymentTermRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param int $accountId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDatatable($accountId = 0)
    {
        $datatable = new PaymentTermDatatable(false);

        $query = $this->paymentTermRepo->find($accountId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function columns($entityType, $hideClient): array
    {
        return [
            [
                'name',
                fn ($model) => link_to("payment_terms/{$model->public_id}/edit", $model->name)->toHtml(),
            ],
            [
                'days',
                fn ($model) => $model->num_days,
            ],
        ];
    }

    public function actions($entityType): array
    {
        return [
            [
                uctrans('texts.edit_payment_terms'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("payment_terms/{$model->public_id}/edit"),
            ],
        ];
    }

    /**
     * @return PaymentTermRepository
     */
    protected function getRepo()
    {
        return $this->paymentTermRepo;
    }
}
