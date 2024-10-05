<?php

namespace App\Ninja\Datatables;

use App\Models\Task;
use App\Models\TaskStatus;
use DropdownButton;
use Utils;

class TaskDatatable extends EntityDatatable
{
    public $entityType = ENTITY_TASK;

    public $sortCol = 3;

    public function columns(): array
    {
        return [
            [
                'client_name',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CLIENT, $model])) {
                        return $model->client_public_id ? link_to("clients/{$model->client_public_id}", Utils::getClientDisplayName($model))->toHtml() : '';
                    }

                    return Utils::getClientDisplayName($model);
                },
                ! $this->hideClient,
            ],
            [
                'project',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_PROJECT, $model])) {
                        return $model->project_public_id ? link_to("projects/{$model->project_public_id}", $model->project)->toHtml() : '';
                    }

                    return $model->project;
                },
            ],
            [
                'date',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_EXPENSE, $model])) {
                        return link_to("tasks/{$model->public_id}/edit", Task::calcStartTime($model))->toHtml();
                    }

                    return Task::calcStartTime($model);
                },
            ],
            [
                'duration',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_EXPENSE, $model])) {
                        return link_to("tasks/{$model->public_id}/edit", Utils::formatTime(Task::calcDuration($model)))->toHtml();
                    }

                    return Utils::formatTime(Task::calcDuration($model));
                },
            ],
            [
                'description',
                fn ($model) => $this->showWithTooltip($model->description),
            ],
            [
                'status',
                fn ($model) => self::getStatusLabel($model),
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_task'),
                fn ($model)       => \Illuminate\Support\Facades\URL::to('tasks/' . $model->public_id . '/edit'),
                fn ($model): bool => ( ! $model->deleted_at || $model->deleted_at == '0000-00-00') && \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_TASK, $model]),
            ],
            [
                trans('texts.view_invoice'),
                fn ($model)       => \Illuminate\Support\Facades\URL::to("/invoices/{$model->invoice_public_id}/edit"),
                fn ($model): bool => $model->invoice_number && \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_TASK, $model]),
            ],
            [
                trans('texts.resume_task'),
                fn ($model): string => "javascript:submitForm_task('resume', {$model->public_id})",
                fn ($model): bool   => ! $model->is_running && \Illuminate\Support\Facades\Auth::user()->can('edit', [ENTITY_TASK, $model]),
            ],
            [
                trans('texts.stop_task'),
                fn ($model): string => "javascript:submitForm_task('stop', {$model->public_id})",
                fn ($model): bool   => $model->is_running && \Illuminate\Support\Facades\Auth::user()->can('edit', [ENTITY_TASK, $model]),
            ],
            [
                trans('texts.invoice_task'),
                fn ($model): string => "javascript:submitForm_task('invoice', {$model->public_id})",
                fn ($model): bool   => ! $model->is_running && ! $model->invoice_number && ( ! $model->deleted_at || $model->deleted_at == '0000-00-00') && \Illuminate\Support\Facades\Auth::user()->canCreateOrEdit(ENTITY_INVOICE),
            ],
        ];
    }

    public function bulkActions(): array
    {
        $actions = [];

        $statuses = TaskStatus::scope()->orderBy('sort_order')->get();

        foreach ($statuses as $status) {
            $actions[] = [
                'label' => sprintf('%s %s', trans('texts.mark'), $status->name),
                'url'   => 'javascript:submitForm_' . $this->entityType . '("update_status:' . $status->public_id . '")',
            ];
        }

        if (count($actions)) {
            $actions[] = DropdownButton::DIVIDER;
        }

        $actions = array_merge($actions, parent::bulkActions());

        return $actions;
    }

    private function getStatusLabel($model): string
    {
        $label = Task::calcStatusLabel($model->is_running, $model->balance, $model->invoice_number, $model->task_status);
        $class = Task::calcStatusClass($model->is_running, $model->balance, $model->invoice_number);

        return "<h4><div class=\"label label-{$class}\">{$label}</div></h4>";
    }
}
