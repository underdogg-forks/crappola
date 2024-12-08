<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Utils;

class ProductDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PRODUCT;

    public $sortCol = 4;

    public function columns(): array
    {
        $account = Auth::user()->account;

        return [
            [
                'product_key',
                fn ($model) => link_to('products/' . $model->public_id . '/edit', $model->product_key)->toHtml(),
            ],
            [
                'notes',
                fn ($model) => $this->showWithTooltip($model->notes),
            ],
            [
                'cost',
                fn ($model) => Utils::roundSignificant($model->cost),
            ],
            [
                'tax_rate',
                fn ($model): string => $model->tax_rate ? ($model->tax_name . ' ' . $model->tax_rate . '%') : '',
                $account->invoice_item_taxes,
            ],
            [
                'custom_value1',
                fn ($model) => $model->custom_value1,
                $account->customLabel('product1'),
            ],
            [
                'custom_value2',
                fn ($model) => $model->custom_value2,
                $account->customLabel('product2'),
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                uctrans('texts.edit_product'),
                fn ($model) => URL::to(sprintf('products/%s/edit', $model->public_id)),
            ],
            [
                trans('texts.clone_product'),
                fn ($model) => URL::to(sprintf('products/%s/clone', $model->public_id)),
                fn ($model) => Auth::user()->can('create', ENTITY_PRODUCT),
            ],
            [
                trans('texts.invoice_product'),
                fn ($model): string => sprintf("javascript:submitForm_product('invoice', %s)", $model->public_id),
                fn ($model): bool   => ( ! $model->deleted_at || $model->deleted_at == '0000-00-00') && Auth::user()->can('create', ENTITY_INVOICE),
            ],
        ];
    }
}
