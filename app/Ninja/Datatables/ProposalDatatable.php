<?php

namespace App\Ninja\Datatables;

use Utils;

class ProposalDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PROPOSAL;

    public $sortCol = 1;

    public function columns(): array
    {
        return [
            [
                'quote',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_QUOTE, $model])) {
                        return link_to("quotes/{$model->invoice_public_id}", $model->invoice_number)->toHtml();
                    }

                    return $model->invoice_number;
                },
            ],
            [
                'client',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CLIENT, $model])) {
                        return link_to("clients/{$model->client_public_id}", $model->client)->toHtml();
                    }

                    return $model->client;
                },
            ],
            [
                'template',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL_TEMPLATE, $model])) {
                        return link_to("proposals/templates/{$model->template_public_id}/edit", $model->template ?: ' ')->toHtml();
                    }

                    return $model->template ?: ' ';
                },
            ],
            [
                'created_at',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL, $model])) {
                        return link_to("proposals/{$model->public_id}/edit", Utils::timestampToDateString(strtotime($model->created_at)))->toHtml();
                    }

                    return Utils::timestampToDateString(strtotime($model->created_at));
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
                trans('texts.edit_proposal'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("proposals/{$model->public_id}/edit"),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROPOSAL, $model]),
            ],
        ];
    }
}
