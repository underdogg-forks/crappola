<?php

namespace App\Services;

use App\Ninja\Datatables\ExpenseCategoryDatatable;
use App\Ninja\Repositories\ExpenseCategoryRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class ExpenseCategoryService.
 */
class ExpenseCategoryService extends BaseService
{
    /**
     * @var ExpenseCategoryRepository
     */
    protected $categoryRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * CreditService constructor.
     *
     * @param ExpenseCategoryRepository $creditRepo
     */
    public function __construct(ExpenseCategoryRepository $categoryRepo, DatatableService $datatableService)
    {
        $this->categoryRepo = $categoryRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @return mixed|null
     */
    public function save($data)
    {
        return $this->categoryRepo->save($data);
    }

    /**
     * @param $clientPublicId
     *
     */
    public function getDatatable($search)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new ExpenseCategoryDatatable();

        $query = $this->categoryRepo->find($search);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return CreditRepository
     */
    protected function getRepo()
    {
        return $this->categoryRepo;
    }
}
