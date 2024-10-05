<?php

namespace App\Ninja\Datatables;

use Auth;
use URL;

class ProposalCategoryDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PROPOSAL_CATEGORY;

    public $sortCol = 1;

    public function columns()
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

    public function actions()
    {
        return [
            [
                trans('texts.edit_category'),
                function ($model) {
                    return \Illuminate\Support\Facades\URL::to("proposals/categories/{$model->public_id}/edit");
                },
                function ($model) {
                    return \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL_CATEGORY, $model]);
                },
            ],
        ];
    }
}
