<?php

namespace App\Ninja\Datatables;

use Utils;

class ProjectDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PROJECT;

    public $sortCol = 1;

    public function columns(): array
    {
        return [
            [
                'project',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROJECT, $model])) {
                        return $this->addNote(link_to('projects/' . $model->public_id, $model->project)->toHtml(), $model->private_notes);
                    }

                    return $model->project;
                },
            ],
            [
                'client_name',
                function ($model) {
                    if ($model->client_public_id) {
                        if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CLIENT, $model])) {
                            return link_to('clients/' . $model->client_public_id, $model->client_name)->toHtml();
                        }

                        return Utils::getClientDisplayName($model);
                    }

                    return '';
                },
            ],
            [
                'due_date',
                fn ($model) => Utils::fromSqlDate($model->due_date),
            ],
            [
                'budgeted_hours',
                fn ($model) => $model->budgeted_hours ?: '',
            ],
            [
                'task_rate',
                fn ($model) => (float) ($model->task_rate) !== 0.0 ? Utils::formatMoney($model->task_rate) : '',
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_project'),
                fn ($model) => \Illuminate\Support\Facades\URL::to(sprintf('projects/%s/edit', $model->public_id)),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROJECT, $model]),
            ],
            [
                trans('texts.invoice_project'),
                fn ($model): string => sprintf("javascript:submitForm_project('invoice', %s)", $model->public_id),
                fn ($model)         => \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_INVOICE),
            ],
        ];
    }
}
