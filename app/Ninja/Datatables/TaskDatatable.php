<?php

namespace App\Ninja\Datatables;

use App\Models\Task;
use App\Models\TaskStatus;
use DropdownButton;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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
                    if (Auth::user()->can('view', [ENTITY_CLIENT, $model])) {
                        return $model->client_public_id ? link_to('clients/' . $model->client_public_id, Utils::getClientDisplayName($model))->toHtml() : '';
                    }

                    return Utils::getClientDisplayName($model);
                },
                ! $this->hideClient,
            ],
            [
                'project',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_PROJECT, $model])) {
                        return $model->project_public_id ? link_to('projects/' . $model->project_public_id, $model->project)->toHtml() : '';
                    }

                    return $model->project;
                },
            ],
            [
                'date',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_EXPENSE, $model])) {
                        return link_to(sprintf('tasks/%s/edit', $model->public_id), Task::calcStartTime($model))->toHtml();
                    }

                    return Task::calcStartTime($model);
                },
            ],
            [
                'duration',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_EXPENSE, $model])) {
                        return link_to(sprintf('tasks/%s/edit', $model->public_id), Utils::formatTime(Task::calcDuration($model)))->toHtml();
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
                fn ($model)       => URL::to('tasks/' . $model->public_id . '/edit'),
                fn ($model): bool => ( ! $model->deleted_at || $model->deleted_at == '0000-00-00') && Auth::user()->can('view', [ENTITY_TASK, $model]),
            ],
            [
                trans('texts.view_invoice'),
                fn ($model)       => URL::to(sprintf('/invoices/%s/edit', $model->invoice_public_id)),
                fn ($model): bool => $model->invoice_number && Auth::user()->can('view', [ENTITY_TASK, $model]),
            ],
            [
                trans('texts.resume_task'),
                fn ($model): string => sprintf("javascript:submitForm_task('resume', %s)", $model->public_id),
                fn ($model): bool   => ! $model->is_running && Auth::user()->can('edit', [ENTITY_TASK, $model]),
            ],
            [
                trans('texts.stop_task'),
                fn ($model): string => sprintf("javascript:submitForm_task('stop', %s)", $model->public_id),
                fn ($model): bool   => $model->is_running && Auth::user()->can('edit', [ENTITY_TASK, $model]),
            ],
            [
                trans('texts.invoice_task'),
                fn ($model): string => sprintf("javascript:submitForm_task('invoice', %s)", $model->public_id),
                fn ($model): bool   => ! $model->is_running && ! $model->invoice_number && ( ! $model->deleted_at || $model->deleted_at == '0000-00-00') && Auth::user()->canCreateOrEdit(ENTITY_INVOICE),
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

        if ($actions !== []) {
            $actions[] = DropdownButton::DIVIDER;
        }

        $actions = array_merge($actions, parent::bulkActions());

        return $actions;
    }

    private function getStatusLabel($model): string
    {
        $label = Task::calcStatusLabel($model->is_running, $model->balance, $model->invoice_number, $model->task_status);
        $class = Task::calcStatusClass($model->is_running, $model->balance, $model->invoice_number);

        return sprintf('<h4><div class="label label-%s">%s</div></h4>', $class, $label);
    }
}
