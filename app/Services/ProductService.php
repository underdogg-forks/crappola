<?php

namespace App\Services;

use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Repositories\ProductRepository;
use Auth;
use Illuminate\Http\JsonResponse;
use App\Libraries\Utils;

class ProductService extends BaseService
{
    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * @var ProductRepository
     */
    protected $productRepo;

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
     * @param $companyId
     * @param mixed $search
     *
     * @return JsonResponse
     */
    public function getDatatable($companyId, $search)
    {
        $datatable = new ProductDatatable(true);
        $query = $this->productRepo->find($companyId, $search);

        if (!Utils::hasPermission('view_product')) {
            $query->where('products.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return ProductRepository
     */
    protected function getRepo()
    {
        return $this->productRepo;
    }
}
