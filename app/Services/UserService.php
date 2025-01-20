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
    /**
     * @var UserRepository
     */
    protected $userRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

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
     * @param $companyId
     *
     * @return JsonResponse
     */
    public function getDatatable($companyId)
    {
        $datatable = new UserDatatable(false);
        $query = $this->userRepo->find($companyId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return UserRepository
     */
    protected function getRepo()
    {
        return $this->userRepo;
    }
}
