<?php

namespace App\Ninja\Datatables;

use Utils;

class ClientDatatable extends EntityDatatable
{
    public $entityType = ENTITY_CLIENT;

    public $sortCol = 4;

    public function columns(): array
    {
        return [
            [
                'name',
                function ($model): string {
                    $str = link_to("clients/{$model->public_id}", $model->name ?: '')->toHtml();

                    return $this->addNote($str, $model->private_notes);
                },
            ],
            [
                'contact',
                fn ($model) => link_to("clients/{$model->public_id}", $model->contact ?: '')->toHtml(),
            ],
            [
                'email',
                fn ($model) => link_to("clients/{$model->public_id}", $model->email ?: '')->toHtml(),
            ],
            [
                'id_number',
                fn ($model) => $model->id_number,
                \Illuminate\Support\Facades\Auth::user()->account->clientNumbersEnabled(),
            ],
            [
                'client_created_at',
                fn ($model) => Utils::timestampToDateString(strtotime($model->created_at)),
            ],
            [
                'last_login',
                fn ($model) => Utils::timestampToDateString(strtotime($model->last_login)),
            ],
            [
                'balance',
                fn ($model) => Utils::formatMoney($model->balance, $model->currency_id, $model->country_id),
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_client'),
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('edit', [ENTITY_CLIENT, $model])) {
                        return \Illuminate\Support\Facades\URL::to("clients/{$model->public_id}/edit");
                    }
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CLIENT, $model])) {
                        return \Illuminate\Support\Facades\URL::to("clients/{$model->public_id}");
                    }
                },
            ],
            [
                '--divider--', fn (): bool => false,
                fn ($model): bool => \Illuminate\Support\Facades\Auth::user()->can('edit', [ENTITY_CLIENT, $model]) && (\Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_TASK) || \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_INVOICE)),
            ],
            [
                trans('texts.new_task'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("tasks/create/{$model->public_id}"),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_TASK),
            ],
            [
                trans('texts.new_invoice'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("invoices/create/{$model->public_id}"),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_INVOICE),
            ],
            [
                trans('texts.new_quote'),
                fn ($model)       => \Illuminate\Support\Facades\URL::to("quotes/create/{$model->public_id}"),
                fn ($model): bool => \Illuminate\Support\Facades\Auth::user()->hasFeature(FEATURE_QUOTES) && \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_QUOTE),
            ],
            [
                '--divider--', fn (): bool => false,
                fn ($model): bool => (\Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_TASK) || \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_INVOICE)) && (\Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_PAYMENT) || \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_CREDIT) || \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_EXPENSE)),
            ],
            [
                trans('texts.enter_payment'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("payments/create/{$model->public_id}"),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_PAYMENT),
            ],
            [
                trans('texts.enter_credit'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("credits/create/{$model->public_id}"),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_CREDIT),
            ],
            [
                trans('texts.enter_expense'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("expenses/create/{$model->public_id}"),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_EXPENSE),
            ],
        ];
    }
}
