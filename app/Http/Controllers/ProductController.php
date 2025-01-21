<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\TaxRate;
use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Utils;

/**
 * Class ProductController.
 */
class ProductController extends BaseController
{
    protected ProductService $productService;

    protected ProductRepository $productRepo;

    /**
     * ProductController constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService, ProductRepository $productRepo)
    {
        //parent::__construct();

        $this->productService = $productService;
        $this->productRepo = $productRepo;
    }

    /**
     * @return RedirectResponse
     */
    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_PRODUCT,
            'datatable'  => new ProductDatatable(),
            'title'      => trans('texts.products'),
            'statuses'   => Product::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to(sprintf('products/%s/edit', $publicId));
    }

    /**
     * @return JsonResponse
     */
    public function getDatatable()
    {
        return $this->productService->getDatatable(Auth::user()->account_id, Request::input('sSearch'));
    }

    public function cloneProduct(ProductRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    /**
     * @param $publicId
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(ProductRequest $request, string $publicId, $clone = false)
    {
        Auth::user()->can('view', [ENTITY_PRODUCT, $request->entity()]);

        $account = Auth::user()->account;
        $product = Product::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $product->id = null;
            $product->public_id = null;
            $product->deleted_at = null;
            $url = 'products';
            $method = 'POST';
        } else {
            $url = 'products/' . $publicId;
            $method = 'PUT';
        }

        $data = [
            'account'  => $account,
            'taxRates' => $account->invoice_item_taxes ? TaxRate::scope()->whereIsInclusive(false)->get() : null,
            'product'  => $product,
            'entity'   => $product,
            'method'   => $method,
            'url'      => $url,
            'title'    => trans('texts.edit_product'),
        ];

        return View::make('accounts.product', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create(ProductRequest $request)
    {
        $account = Auth::user()->account;

        $data = [
            'account'  => $account,
            'taxRates' => $account->invoice_item_taxes ? TaxRate::scope()->whereIsInclusive(false)->get(['id', 'name', 'rate']) : null,
            'product'  => null,
            'method'   => 'POST',
            'url'      => 'products',
            'title'    => trans('texts.create_product'),
        ];

        return View::make('accounts.product', $data);
    }

    /**
     * @return RedirectResponse
     */
    public function store(CreateProductRequest $request)
    {
        return $this->save();
    }

    /**
     * @param $publicId
     *
     * @return RedirectResponse
     */
    public function update(UpdateProductRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    /**
     * @return RedirectResponse
     */
    public function bulk()
    {
        $action = Request::input('action');
        $ids = Request::input('public_id') ?: Request::input('ids');

        if ($action == 'invoice') {
            $products = Product::scope($ids)->get();
            $data = [];
            foreach ($products as $product) {
                $data[] = $product->product_key;
            }

            return redirect('invoices/create')->with('selectedProducts', $data);
        }

        $count = $this->productService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_product', $count);
        Session::flash('message', $message);

        return $this->returnBulk(ENTITY_PRODUCT, $action, $ids);
    }

    /**
     * @param bool $productPublicId
     *
     * @return RedirectResponse
     */
    private function save($productPublicId = false)
    {
        if ($productPublicId) {
            $product = Product::scope($productPublicId)->withTrashed()->firstOrFail();
        } else {
            $product = Product::createNew();
        }

        $this->productRepo->save(Request::all(), $product);

        $message = $productPublicId ? trans('texts.updated_product') : trans('texts.created_product');
        Session::flash('message', $message);

        $action = request('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('products/%s/clone', $product->public_id));
        }

        return redirect()->to(sprintf('products/%s/edit', $product->public_id));
    }
}
