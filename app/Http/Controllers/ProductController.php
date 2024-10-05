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
use Auth;
use Redirect;
use Request;
use Session;
use Utils;
use View;

/**
 * Class ProductController.
 */
class ProductController extends BaseController
{
    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var ProductRepository
     */
    protected $productRepo;

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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return \Illuminate\Support\Facades\View::make('list_wrapper', [
            'entityType' => ENTITY_PRODUCT,
            'datatable'  => new ProductDatatable(),
            'title'      => trans('texts.products'),
            'statuses'   => Product::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        \Illuminate\Support\Facades\Session::reflash();

        return \Illuminate\Support\Facades\Redirect::to("products/{$publicId}/edit");
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDatatable()
    {
        return $this->productService->getDatatable(\Illuminate\Support\Facades\Auth::user()->account_id, \Illuminate\Support\Facades\Request::input('sSearch'));
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
    public function edit(ProductRequest $request, $publicId, $clone = false)
    {
        \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PRODUCT, $request->entity()]);

        $account = \Illuminate\Support\Facades\Auth::user()->account;
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

        return \Illuminate\Support\Facades\View::make('accounts.product', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create(ProductRequest $request)
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;

        $data = [
            'account'  => $account,
            'taxRates' => $account->invoice_item_taxes ? TaxRate::scope()->whereIsInclusive(false)->get(['id', 'name', 'rate']) : null,
            'product'  => null,
            'method'   => 'POST',
            'url'      => 'products',
            'title'    => trans('texts.create_product'),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.product', $data);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateProductRequest $request)
    {
        return $this->save();
    }

    /**
     * @param $publicId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, $publicId)
    {
        return $this->save($publicId);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('action');
        $ids = \Illuminate\Support\Facades\Request::input('public_id') ? \Illuminate\Support\Facades\Request::input('public_id') : \Illuminate\Support\Facades\Request::input('ids');

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
        \Illuminate\Support\Facades\Session::flash('message', $message);

        return $this->returnBulk(ENTITY_PRODUCT, $action, $ids);
    }

    /**
     * @param bool $productPublicId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($productPublicId = false)
    {
        if ($productPublicId) {
            $product = Product::scope($productPublicId)->withTrashed()->firstOrFail();
        } else {
            $product = Product::createNew();
        }

        $this->productRepo->save(\Illuminate\Support\Facades\Request::all(), $product);

        $message = $productPublicId ? trans('texts.updated_product') : trans('texts.created_product');
        \Illuminate\Support\Facades\Session::flash('message', $message);

        $action = request('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('products/%s/clone', $product->public_id));
        }

        return redirect()->to("products/{$product->public_id}/edit");
    }
}
