<?php

namespace App\Services;

use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Repositories\ProductRepository;
use Utils;

class ProductService extends BaseService
{
    protected \App\Services\DatatableService $datatableService;

    protected \App\Ninja\Repositories\ProductRepository $productRepo;

    /**
     * ProductService constructor.
     *
     * @param DatatableService  $datatableService
     * @param ProductRepository $productRepo
     */
    public function __construct(DatatableService $datatableService, ProductRepository $productRepo)
    {
        $this->datatableService = $datatableService;
        $this->productRepo = $productRepo;
    }

    /**
     * @param       $accountId
     * @param mixed $search
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDatatable($accountId, $search)
    {
        $datatable = new ProductDatatable(true);
        $query = $this->productRepo->find($accountId, $search);

        if ( ! Utils::hasPermission('view_product')) {
            $query->where('products.user_id', '=', \Illuminate\Support\Facades\Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return ProductRepository
     */
    protected function getRepo(): \App\Ninja\Repositories\ProductRepository
    {
        return $this->productRepo;
    }
}
