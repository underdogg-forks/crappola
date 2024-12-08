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
     *
     * @param UserRepository   $userRepo
     * @param DatatableService $datatableService
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

    /**
     * @return UserRepository
     */
    protected function getRepo(): UserRepository
    {
        return $this->userRepo;
    }
}
