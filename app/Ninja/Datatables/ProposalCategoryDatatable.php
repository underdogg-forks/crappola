<?php

namespace App\Ninja\Datatables;

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
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL_CATEGORY, $model])) {
                        return link_to("proposals/categories/{$model->public_id}/edit", $model->name)->toHtml();
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
                fn ($model) => \Illuminate\Support\Facades\URL::to("proposals/categories/{$model->public_id}/edit"),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL_CATEGORY, $model]),
            ],
        ];
    }
}
