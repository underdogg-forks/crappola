<?php

namespace App\Services;

use App\Ninja\Datatables\UserDatatable;
use App\Ninja\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class UserService.
 */
class UserService extends BaseService
{
    protected UserRepository $userRepo;

    protected DatatableService $datatableService;

    /**
     * UserService constructor.
     */
    public function __construct(UserRepository $userRepo, DatatableService $datatableService)
    {
        $this->userRepo = $userRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param $accountId
     *
     * @return JsonResponse
     */
    public function getDatatable($accountId)
    {
        $datatable = new UserDatatable(false);
        $query = $this->userRepo->find($accountId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    protected function getRepo(): UserRepository
    {
        return $this->userRepo;
    }
}
