<?php

namespace App\Ninja\Datatables;

class ProposalSnippetDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PROPOSAL_SNIPPET;

    public $sortCol = 1;

    public function columns(): array
    {
        return [
            [
                'name',
                function ($model): string {
                    $icon = '<i class="fa fa-' . $model->icon . '"></i>&nbsp;&nbsp;';

                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL_SNIPPET, $model])) {
                        return $icon . link_to("proposals/snippets/{$model->public_id}/edit", $model->name)->toHtml();
                    }

                    return $icon . $model->name;
                },
            ],
            [
                'category',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL_CATEGORY, $model])) {
                        return link_to("proposals/categories/{$model->category_public_id}/edit", $model->category ?: ' ')->toHtml();
                    }

                    return $model->category;
                },
            ],
            [
                'content',
                fn ($model) => $this->showWithTooltip(strip_tags($model->content)),
            ],
            [
                'private_notes',
                fn ($model) => $this->showWithTooltip($model->private_notes),
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_proposal_snippet'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("proposals/snippets/{$model->public_id}/edit"),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL_SNIPPET, $model]),
            ],
        ];
    }
}
