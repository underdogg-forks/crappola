<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Product;
use App\Ninja\Datatables\ProductDatatable;
use App\Ninja\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
     */
    public function __construct(DatatableService $datatableService, ProductRepository $productRepo)
    {
        $this->datatableService = $datatableService;
        $this->productRepo = $productRepo;
    }

    /**
     * @param mixed $search
     *
     */
    public function getDatatable($companyId, $search)
    {
        $productsDatatable  = new ProductDatatable();
        $query = Product::query()
            /*->with(['taxRate' => function ($query): void {
                $query->select('tax_rates.name', 'tax_rates.rate', 'tax_rates.is_inclusive');
            }])*/
            ->select(
                'products.id',
                'products.product_key',
                'products.cost',
                'products.default_tax_rate_id',
                'products.deleted_at',
            )
            ->where('products.company_id', '=', $companyId);

        return datatables()
            ->eloquent($query) // Adjust the query to your model
            ->addIndexColumn()
            ->setRowId('id')
            ->addColumn('checkbox', function ($model) {
                return '<input type="checkbox" name="ids[]" value="' . $model->id
                    . '" ' . Utils::getEntityRowClass($model) . '>';
            })
            ->addColumn('product_key', function ($model) {
                return link_to('products/' . $model->id . '/edit', $model->product_key)->toHtml();
            })
            ->addColumn('cost', function ($model) {
                return Utils::roundSignificant($model->cost);
            })
            /*->addColumn('tax_rate', function ($model) {
                return $model->default_tax_rate_id ? ($model?->default_tax_rate_id . ' ' . ($model?->default_tax_rate_id + 0) . '%') : '';
            })*/
            /*->addColumn('dropdown', function ($model) use ($productsDatatable) {
                $hasAction = false;
                $str = '<div style="min-width:100px">';

                $dropdown_contents = '';

                $lastIsDivider = false;
                if (! property_exists($model, 'is_deleted') || ! $model->is_deleted) {
                    foreach ($productsDatatable->actions() as $action) {
                        if (count($action)) {
                            // if show function isn't set default to true
                            if (count($action) == 2) {
                                $action[] = function () {
                                    return true;
                                };
                            }
                            [$value, $url, $visible] = $action;
                            if ($visible($model)) {
                                if ($value == '--divider--') {
                                    $dropdown_contents .= '<li class="divider"></li>';
                                    $lastIsDivider = true;
                                } else {
                                    $urlVal = $url($model);
                                    $urlStr = is_string($urlVal) ? $urlVal : $urlVal['url'];
                                    $attributes = '';
                                    if (! empty($urlVal['attributes'])) {
                                        $attributes = ' ' . $urlVal['attributes'];
                                    }

                                    $dropdown_contents .= "<li><a href=\"$urlStr\"{$attributes}>{$value}</a></li>";
                                    $hasAction = true;
                                    $lastIsDivider = false;
                                }
                            }
                        } elseif (! $lastIsDivider) {
                            $dropdown_contents .= '<li class="divider"></li>';
                            $lastIsDivider = true;
                        }
                    }

                    if (! $hasAction) {
                        return '';
                    }

                    if ($lastIsDivider) {
                        $dropdown_contents .= '<li class="divider"></li>';
                    }

                    if (! $model->deleted_at || $model->deleted_at == '0000-00-00') {
                        if (($productsDatatable->entityType != ENTITY_USER || $model->id)) {
                            $dropdown_contents .= "<li><a href=\"javascript:submitForm_{$productsDatatable->entityType}('archive', {$model->id})\">"
                                . mtrans($productsDatatable->entityType, "archive_{$productsDatatable->entityType}") . '</a></li>';
                        }
                    }
                }

                if ($model->deleted_at && $model->deleted_at != '0000-00-00') {
                    $dropdown_contents .= "<li><a href=\"javascript:submitForm_{$productsDatatable->entityType}('restore', {$model->id})\">"
                        . mtrans($productsDatatable->entityType, "restore_{$productsDatatable->entityType}") . '</a></li>';
                }

                if (! empty($dropdown_contents)) {
                    $str .= '<div class="btn-group tr-action" style="height:auto">
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" style="width:100px">
                            Select <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">';
                    $str .= $dropdown_contents . '</ul>';
                }

                return $str . '</div></div>';
        })*/
        ->rawColumns(['checkbox', 'product_key', 'dropdown'])
        ->make(true);
    }

    /**
     * @return ProductRepository
     */
    protected function getRepo()
    {
        return $this->productRepo;
    }
}
