<?php

namespace App\Services;

use App\Ninja\Datatables\TaxRateDatatable;
use App\Ninja\Repositories\TaxRateRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class TaxRateService.
 */
class TaxRateService extends BaseService
{
    protected TaxRateRepository $taxRateRepo;

    protected DatatableService $datatableService;

    /**
     * TaxRateService constructor.
     *
     * @param TaxRateRepository $taxRateRepo
     * @param DatatableService  $datatableService
     */
    public function __construct(TaxRateRepository $taxRateRepo, DatatableService $datatableService)
    {
        $this->taxRateRepo = $taxRateRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param $accountId
     *
     * @return JsonResponse
     */
    public function getDatatable($accountId)
    {
        $datatable = new TaxRateDatatable(false);
        $query = $this->taxRateRepo->find($accountId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return TaxRateRepository
     */
    protected function getRepo(): TaxRateRepository
    {
        return $this->taxRateRepo;
    }
}
