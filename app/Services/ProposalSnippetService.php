<?php

namespace App\Services;

use App\Models\Client;
use App\Ninja\Datatables\ProposalSnippetDatatable;
use App\Ninja\Repositories\ProposalSnippetRepository;

/**
 * Class ProposalSnippetService.
 */
class ProposalSnippetService extends BaseService
{
    protected \App\Ninja\Repositories\ProposalSnippetRepository $proposalSnippetRepo;

    protected \App\Services\DatatableService $datatableService;

    /**
     * CreditService constructor.
     *
     * @param ProposalSnippetRepository $creditRepo
     * @param DatatableService          $datatableService
     */
    public function __construct(ProposalSnippetRepository $proposalSnippetRepo, DatatableService $datatableService)
    {
        $this->proposalSnippetRepo = $proposalSnippetRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param       $data
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
     * @param       $search
     * @param mixed $userId
     *
     * @return \Illuminate\Http\JsonResponse
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
    protected function getRepo(): \App\Ninja\Repositories\ProposalSnippetRepository
    {
        return $this->proposalSnippetRepo;
    }
}
