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
    /**
     * @var TokenRepository
     */
    protected $tokenRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * TokenService constructor.
     */
    public function __construct(TokenRepository $tokenRepo, DatatableService $datatableService)
    {
        $this->tokenRepo = $tokenRepo;
        $this->datatableService = $datatableService;
    }

    public function getDatatable($userId)
    {
        $datatable = new TokenDatatable(false);
        $query = $this->tokenRepo->find($userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return TokenRepository
     */
    protected function getRepo()
    {
        return $this->tokenRepo;
    }
}
