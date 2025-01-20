<?php

namespace App\Services;

use App\Ninja\Datatables\PaymentTermDatatable;
use App\Ninja\Repositories\PaymentTermRepository;
use Illuminate\Http\JsonResponse;
use URL;

class PaymentTermService extends BaseService
{
    protected $paymentTermRepo;

    protected $datatableService;

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
     * @param int $companyId
     *
     * @return JsonResponse
     */
    public function getDatatable($companyId = 0)
    {
        $datatable = new PaymentTermDatatable(false);

        $query = $this->paymentTermRepo->find($companyId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    public function columns($entityType, $hideClient)
    {
        return [
            [
                'name',
                function ($model) {
                    return link_to("payment_terms/{$model->public_id}/edit", $model->name)->toHtml();
                },
            ],
            [
                'days',
                function ($model) {
                    return $model->num_days;
                },
            ],
        ];
    }

    public function actions($entityType)
    {
        return [
            [
                uctrans('texts.edit_payment_terms'),
                function ($model) {
                    return URL::to("payment_terms/{$model->public_id}/edit");
                },
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
