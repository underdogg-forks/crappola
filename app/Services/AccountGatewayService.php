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
    protected AccountGatewayRepository $accountGatewayRepo;

    protected DatatableService $datatableService;

    /**
     * AccountGatewayService constructor.
     */
    public function __construct(AccountGatewayRepository $accountGatewayRepo, DatatableService $datatableService)
    {
        $this->accountGatewayRepo = $accountGatewayRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param $accountId
     *
     * @return JsonResponse
     */
    public function getDatatable($accountId)
    {
        $query = $this->accountGatewayRepo->find($accountId);

        return $this->datatableService->createDatatable(new AccountGatewayDatatable(false), $query);
    }

    protected function getRepo(): AccountGatewayRepository
    {
        return $this->accountGatewayRepo;
    }
}
