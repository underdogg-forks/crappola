<?php

namespace App\Services;

use App\Ninja\Datatables\ProposalDatatable;
use App\Ninja\Repositories\ProposalRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class ProposalService.
 */
class ProposalService extends BaseService
{
    /**
     * @var ProposalRepository
     */
    protected $proposalRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * CreditService constructor.
     *
     * @param ProposalRepository $creditRepo
     */
    public function __construct(ProposalRepository $proposalRepo, DatatableService $datatableService)
    {
        $this->proposalRepo = $proposalRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param mixed $proposal
     *
     * @return mixed|null
     */
    public function save($data, $proposal = false)
    {
        return $this->proposalRepo->save($data, $proposal);
    }

    /**
     * @param       $clientPublicId
     * @param mixed $userId
     *
     */
    public function getDatatable($search, $userId)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ProposalDatatable();

        $query = $this->proposalRepo->find($search, $userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return CreditRepository
     */
    protected function getRepo()
    {
        return $this->proposalRepo;
    }
}
