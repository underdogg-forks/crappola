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
    /**
     * @var TaxRateRepository
     */
    protected $taxRateRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

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
     * @param $companyId
     *
     * @return JsonResponse
     */
    public function getDatatable($companyId)
    {
        $datatable = new TaxRateDatatable(false);
        $query = $this->taxRateRepo->find($companyId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return TaxRateRepository
     */
    protected function getRepo()
    {
        return $this->taxRateRepo;
    }
}
