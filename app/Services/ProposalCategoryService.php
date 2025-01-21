<?php

namespace App\Services;

use App\Models\Client;
use App\Ninja\Datatables\ProposalCategoryDatatable;
use App\Ninja\Repositories\ProposalCategoryRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class ProposalCategoryService.
 */
class ProposalCategoryService extends BaseService
{
    protected ProposalCategoryRepository $proposalCategoryRepo;

    protected DatatableService $datatableService;

    /**
     * CreditService constructor.
     *
     * @param ProposalCategoryRepository $creditRepo
     * @param DatatableService           $datatableService
     */
    public function __construct(ProposalCategoryRepository $proposalCategoryRepo, DatatableService $datatableService)
    {
        $this->proposalCategoryRepo = $proposalCategoryRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param       $data
     * @param mixed $proposalCategory
     *
     * @return mixed|null
     */
    public function save($data, $proposalCategory = false)
    {
        return $this->proposalCategoryRepo->save($data, $proposalCategory);
    }

    /**
     * @param       $clientPublicId
     * @param       $search
     * @param mixed $userId
     *
     * @return JsonResponse
     */
    public function getDatatable($search, $userId)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ProposalCategoryDatatable();

        $query = $this->proposalCategoryRepo->find($search, $userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return CreditRepository
     */
    protected function getRepo(): ProposalCategoryRepository
    {
        return $this->proposalCategoryRepo;
    }
}
