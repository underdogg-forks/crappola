<?php

namespace App\Ninja\Datatables;

use Utils;

class CreditDatatable extends EntityDatatable
{
    public $entityType = ENTITY_CREDIT;

    public $sortCol = 4;

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
                'amount',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CLIENT, $model])) {
                        return Utils::formatMoney($model->amount, $model->currency_id, $model->country_id) . '<span ' . Utils::getEntityRowClass($model) . '/>';
                    }
                },
            ],
            [
                'balance',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CLIENT, $model])) {
                        return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                    }
                },
            ],
            [
                'credit_date',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CREDIT, $model])) {
                        return link_to("credits/{$model->public_id}/edit", Utils::fromSqlDate($model->credit_date_sql))->toHtml();
                    }

                    return Utils::fromSqlDate($model->credit_date_sql);
                },
            ],
            [
                'public_notes',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CREDIT, $model])) {
                        return e($model->public_notes);
                    }
                },
            ],
            [
                'private_notes',
                function ($model) {
                    if (\Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CREDIT, $model])) {
                        return e($model->private_notes);
                    }
                },
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_credit'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("credits/{$model->public_id}/edit"),
                fn ($model) => \Illuminate\Support\Facades\Auth::user()->can('view', [ENTITY_CREDIT, $model]),
            ],
            [
                trans('texts.apply_credit'),
                fn ($model): string => \Illuminate\Support\Facades\URL::to("payments/create/{$model->client_public_id}") . '?paymentTypeId=1',
                fn ($model)         => \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_PAYMENT),
            ],
        ];
    }
}
