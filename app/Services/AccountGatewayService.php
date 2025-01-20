<?php

namespace App\Services;

use App\Ninja\Datatables\AccountGatewayDatatable;
use App\Ninja\Repositories\AccountGatewayRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class AccountGatewayService.
 */
class AccountGatewayService extends BaseService
{
    /**
     * @var AccountGatewayRepository
     */
    protected $companyGatewayRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * AccountGatewayService constructor.
     *
     * @param AccountGatewayRepository $companyGatewayRepo
     * @param DatatableService         $datatableService
     */
    public function __construct(AccountGatewayRepository $companyGatewayRepo, DatatableService $datatableService)
    {
        $this->accountGatewayRepo = $companyGatewayRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param $companyId
     *
     * @return JsonResponse
     */
    public function getDatatable($companyId)
    {
        $query = $this->accountGatewayRepo->find($companyId);

        return $this->datatableService->createDatatable(new AccountGatewayDatatable(false), $query);
    }

    /**
     * @return AccountGatewayRepository
     */
    protected function getRepo()
    {
        return $this->accountGatewayRepo;
    }
}
