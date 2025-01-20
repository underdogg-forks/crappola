<?php

namespace App\Services;

use App\Ninja\Datatables\ProposalTemplateDatatable;
use App\Ninja\Repositories\ProposalTemplateRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class ProposalTemplateService.
 */
class ProposalTemplateService extends BaseService
{
    /**
     * @var ProposalTemplateRepository
     */
    protected $proposalTemplateRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * CreditService constructor.
     *
     * @param ProposalTemplateRepository $creditRepo
     */
    public function __construct(ProposalTemplateRepository $proposalTemplateRepo, DatatableService $datatableService)
    {
        $this->proposalTemplateRepo = $proposalTemplateRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param mixed $proposalTemplate
     *
     * @return mixed|null
     */
    public function save($data, $proposalTemplate = false)
    {
        return $this->proposalTemplateRepo->save($data, $proposalTemplate);
    }

    /**
     * @param       $clientPublicId
     * @param mixed $userId
     *
     */
    public function getDatatable($search, $userId)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ProposalTemplateDatatable();

        $query = $this->proposalTemplateRepo->find($search, $userId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return CreditRepository
     */
    protected function getRepo()
    {
        return $this->proposalTemplateRepo;
    }
}
