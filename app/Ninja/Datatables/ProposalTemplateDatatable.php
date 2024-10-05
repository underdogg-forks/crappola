<?php

namespace App\Ninja\Datatables;

class ProposalTemplateDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PROPOSAL_TEMPLATE;

    public $sortCol = 1;

    public function columns(): array
    {
        return [
            [
                'name',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL_TEMPLATE, $model])) {
                        return link_to('proposals/templates/' . $model->public_id, $model->name)->toHtml();
                    }

                    return $model->name;
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
                trans('texts.edit_proposal_template'),
                fn ($model) => \Illuminate\Support\Facades\URL::to(sprintf('proposals/templates/%s/edit', $model->public_id)),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL_TEMPLATE, $model]),
            ],
            [
                trans('texts.clone_proposal_template'),
                fn ($model) => \Illuminate\Support\Facades\URL::to(sprintf('proposals/templates/%s/clone', $model->public_id)),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL_TEMPLATE, $model]),
            ],
            [
                trans('texts.new_proposal'),
                fn ($model) => \Illuminate\Support\Facades\URL::to('proposals/create/0/' . $model->public_id),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('create', [ENTITY_PROPOSAL, $model]),
            ],
        ];
    }
}
