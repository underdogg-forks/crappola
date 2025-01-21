<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ExpenseCategoryDatatable extends EntityDatatable
{
    public $entityType = ENTITY_EXPENSE_CATEGORY;

    public $sortCol = 1;

    public function columns(): array
    {
        return [
            [
                'name',
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_EXPENSE_CATEGORY, $model])) {
                        return link_to(sprintf('expense_categories/%s/edit', $model->public_id), $model->category)->toHtml();
                    }

                    return $model->category;
                },
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_category'),
                fn ($model) => URL::to(sprintf('expense_categories/%s/edit', $model->public_id)),
                fn ($model) => Auth::user()->can('edit', [ENTITY_EXPENSE_CATEGORY, $model]),
            ],
        ];
    }
}
