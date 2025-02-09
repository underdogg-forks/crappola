<?php

namespace App\Services;

use App\Ninja\Datatables\PaymentTermDatatable;
use App\Ninja\Repositories\PaymentTermRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;

class PaymentTermService extends BaseService
{
    protected PaymentTermRepository $paymentTermRepo;

    protected DatatableService $datatableService;

    /**
     * PaymentTermService constructor.
     */
    public function __construct(PaymentTermRepository $paymentTermRepo, DatatableService $datatableService)
    {
        $this->paymentTermRepo = $paymentTermRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param int $accountId
     *
     * @return JsonResponse
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
                fn ($model) => link_to(sprintf('payment_terms/%s/edit', $model->public_id), $model->name)->toHtml(),
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
                fn ($model) => URL::to(sprintf('payment_terms/%s/edit', $model->public_id)),
            ],
        ];
    }

    protected function getRepo(): PaymentTermRepository
    {
        return $this->paymentTermRepo;
    }
}
