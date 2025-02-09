<?php

namespace App\Services;

use App\Ninja\Datatables\TokenDatatable;
use App\Ninja\Repositories\TokenRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class TokenService.
 */
class TokenService extends BaseService
{
    protected TokenRepository $tokenRepo;

    protected DatatableService $datatableService;

    /**
     * TokenService constructor.
     */
    public function __construct(TokenRepository $tokenRepo, DatatableService $datatableService)
    {
        $this->tokenRepo = $tokenRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param $userId
     *
     * @return JsonResponse
     */
    public function getDatatable($userId)
    {
        $datatable = new TokenDatatable(false);
        $query = $this->tokenRepo->find($userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    protected function getRepo(): TokenRepository
    {
        return $this->tokenRepo;
    }
}
