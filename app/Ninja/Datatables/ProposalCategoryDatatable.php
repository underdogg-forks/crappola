<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ProposalCategoryDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PROPOSAL_CATEGORY;

    public $sortCol = 1;

    public function columns(): array
    {
        return [
            [
                'name',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_PROPOSAL_CATEGORY, $model])) {
                        return link_to(sprintf('proposals/categories/%s/edit', $model->public_id), $model->name)->toHtml();
                    }

                    return $model->name;
                },
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_category'),
                fn ($model) => URL::to(sprintf('proposals/categories/%s/edit', $model->public_id)),
                fn ($model) => Auth::user()->can('view', [ENTITY_PROPOSAL_CATEGORY, $model]),
            ],
        ];
    }
}
