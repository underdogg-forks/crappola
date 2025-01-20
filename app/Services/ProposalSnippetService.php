<?php

namespace App\Services;

use App\Ninja\Datatables\ProposalSnippetDatatable;
use App\Ninja\Repositories\ProposalSnippetRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class ProposalSnippetService.
 */
class ProposalSnippetService extends BaseService
{
    /**
     * @var ProposalSnippetRepository
     */
    protected $proposalSnippetRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * CreditService constructor.
     *
     * @param ProposalSnippetRepository $creditRepo
     */
    public function __construct(ProposalSnippetRepository $proposalSnippetRepo, DatatableService $datatableService)
    {
        $this->proposalSnippetRepo = $proposalSnippetRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param mixed $proposalSnippet
     *
     * @return mixed|null
     */
    public function save($data, $proposalSnippet = false)
    {
        return $this->proposalSnippetRepo->save($data, $proposalSnippet);
    }

    /**
     * @param       $clientPublicId
     * @param mixed $userId
     *
     */
    public function getDatatable($search, $userId)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ProposalSnippetDatatable();

        $query = $this->proposalSnippetRepo->find($search, $userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return CreditRepository
     */
    protected function getRepo()
    {
        return $this->proposalSnippetRepo;
    }
}
