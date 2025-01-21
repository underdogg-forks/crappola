<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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
                    if (Auth::user()->can('view', [ENTITY_QUOTE, $model])) {
                        return link_to('quotes/' . $model->invoice_public_id, $model->invoice_number)->toHtml();
                    }

                    return $model->invoice_number;
                },
            ],
            [
                'client',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_CLIENT, $model])) {
                        return link_to('clients/' . $model->client_public_id, $model->client)->toHtml();
                    }

                    return $model->client;
                },
            ],
            [
                'template',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_PROPOSAL_TEMPLATE, $model])) {
                        return link_to(sprintf('proposals/templates/%s/edit', $model->template_public_id), $model->template ?: ' ')->toHtml();
                    }

                    return $model->template ?: ' ';
                },
            ],
            [
                'created_at',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_PROPOSAL, $model])) {
                        return link_to(sprintf('proposals/%s/edit', $model->public_id), Utils::timestampToDateString(strtotime($model->created_at)))->toHtml();
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
                fn ($model) => URL::to(sprintf('proposals/%s/edit', $model->public_id)),
                fn ($model) => Auth::user()->can('view', [ENTITY_PROPOSAL, $model]),
            ],
        ];
    }
}
