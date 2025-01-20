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
     */
    public function __construct(TaxRateRepository $taxRateRepo, DatatableService $datatableService)
    {
        $this->taxRateRepo = $taxRateRepo;
        $this->datatableService = $datatableService;
    }

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
