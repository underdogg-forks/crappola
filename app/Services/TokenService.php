<?php

namespace App\Services;

use App\Ninja\Datatables\TokenDatatable;
use App\Ninja\Repositories\TokenRepository;

/**
 * Class TokenService.
 */
class TokenService extends BaseService
{
    protected \App\Ninja\Repositories\TokenRepository $tokenRepo;

    protected \App\Services\DatatableService $datatableService;

    /**
     * TokenService constructor.
     *
     * @param TokenRepository  $tokenRepo
     * @param DatatableService $datatableService
     */
    public function __construct(TokenRepository $tokenRepo, DatatableService $datatableService)
    {
        $this->tokenRepo = $tokenRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param $userId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDatatable($userId)
    {
        $datatable = new TokenDatatable(false);
        $query = $this->tokenRepo->find($userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return TokenRepository
     */
    protected function getRepo(): \App\Ninja\Repositories\TokenRepository
    {
        return $this->tokenRepo;
    }
}
