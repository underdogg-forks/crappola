<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

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
                    $str = link_to('clients/' . $model->public_id, $model->name ?: '')->toHtml();

                    return $this->addNote($str, $model->private_notes);
                },
            ],
            [
                'contact',
                fn ($model) => link_to('clients/' . $model->public_id, $model->contact ?: '')->toHtml(),
            ],
            [
                'email',
                fn ($model) => link_to('clients/' . $model->public_id, $model->email ?: '')->toHtml(),
            ],
            [
                'id_number',
                fn ($model) => $model->id_number,
                Auth::user()->account->clientNumbersEnabled(),
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
                    if (Auth::user()->can('edit', [ENTITY_CLIENT, $model])) {
                        return URL::to(sprintf('clients/%s/edit', $model->public_id));
                    }

                    if (Auth::user()->can('view', [ENTITY_CLIENT, $model])) {
                        return URL::to('clients/' . $model->public_id);
                    }
                },
            ],
            [
                '--divider--', fn (): bool => false,
                fn ($model): bool => Auth::user()->can('edit', [ENTITY_CLIENT, $model]) && (Auth::user()->can('create', ENTITY_TASK) || Auth::user()->can('create', ENTITY_INVOICE)),
            ],
            [
                trans('texts.new_task'),
                fn ($model) => URL::to('tasks/create/' . $model->public_id),
                fn ($model) => Auth::user()->can('create', ENTITY_TASK),
            ],
            [
                trans('texts.new_invoice'),
                fn ($model) => URL::to('invoices/create/' . $model->public_id),
                fn ($model) => Auth::user()->can('create', ENTITY_INVOICE),
            ],
            [
                trans('texts.new_quote'),
                fn ($model)       => URL::to('quotes/create/' . $model->public_id),
                fn ($model): bool => Auth::user()->hasFeature(FEATURE_QUOTES) && Auth::user()->can('create', ENTITY_QUOTE),
            ],
            [
                '--divider--', fn (): bool => false,
                fn ($model): bool => (Auth::user()->can('create', ENTITY_TASK) || Auth::user()->can('create', ENTITY_INVOICE)) && (Auth::user()->can('create', ENTITY_PAYMENT) || Auth::user()->can('create', ENTITY_CREDIT) || Auth::user()->can('create', ENTITY_EXPENSE)),
            ],
            [
                trans('texts.enter_payment'),
                fn ($model) => URL::to('payments/create/' . $model->public_id),
                fn ($model) => Auth::user()->can('create', ENTITY_PAYMENT),
            ],
            [
                trans('texts.enter_credit'),
                fn ($model) => URL::to('credits/create/' . $model->public_id),
                fn ($model) => Auth::user()->can('create', ENTITY_CREDIT),
            ],
            [
                trans('texts.enter_expense'),
                fn ($model) => URL::to('expenses/create/' . $model->public_id),
                fn ($model) => Auth::user()->can('create', ENTITY_EXPENSE),
            ],
        ];
    }
}
