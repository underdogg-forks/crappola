<?php

namespace App\Ninja\Datatables;

use Auth;
use URL;
use Utils;

class ClientDatatable extends EntityDatatable
{
    public $entityType = ENTITY_CLIENT;

    public $sortCol = 4;

    public function columns()
    {
        return [
            [
                'name',
                function ($model) {
                    $str = link_to("clients/{$model->public_id}", $model->name ?: '')->toHtml();

                    return $this->addNote($str, $model->private_notes);
                },
            ],
            [
                'contact',
                function ($model) {
                    return link_to("clients/{$model->public_id}", $model->contact ?: '')->toHtml();
                },
            ],
            [
                'email',
                function ($model) {
                    return link_to("clients/{$model->public_id}", $model->email ?: '')->toHtml();
                },
            ],
            [
                'id_number',
                function ($model) {
                    return $model->id_number;
                },
                \Illuminate\Support\Facades\Auth::user()->account->clientNumbersEnabled(),
            ],
            [
                'client_created_at',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->created_at));
                },
            ],
            [
                'last_login',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->last_login));
                },
            ],
            [
                'balance',
                function ($model) {
                    return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
                },
            ],
        ];
    }

    public function actions()
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
                '--divider--', function () {
                    return false;
                },
                function ($model) {
                    return \Illuminate\Support\Facades\Auth::user()->can('edit', [ENTITY_CLIENT, $model]) && (\Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_TASK) || \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_INVOICE));
                },
            ],
            [
                trans('texts.new_task'),
                function ($model) {
                    return \Illuminate\Support\Facades\URL::to("tasks/create/{$model->public_id}");
                },
                function ($model) {
                    return \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_TASK);
                },
            ],
            [
                trans('texts.new_invoice'),
                function ($model) {
                    return \Illuminate\Support\Facades\URL::to("invoices/create/{$model->public_id}");
                },
                function ($model) {
                    return \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_INVOICE);
                },
            ],
            [
                trans('texts.new_quote'),
                function ($model) {
                    return \Illuminate\Support\Facades\URL::to("quotes/create/{$model->public_id}");
                },
                function ($model) {
                    return \Illuminate\Support\Facades\Auth::user()->hasFeature(FEATURE_QUOTES) && \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_QUOTE);
                },
            ],
            [
                '--divider--', function () {
                    return false;
                },
                function ($model) {
                    return (\Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_TASK) || \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_INVOICE)) && (\Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_PAYMENT) || \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_CREDIT) || \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_EXPENSE));
                },
            ],
            [
                trans('texts.enter_payment'),
                function ($model) {
                    return \Illuminate\Support\Facades\URL::to("payments/create/{$model->public_id}");
                },
                function ($model) {
                    return \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_PAYMENT);
                },
            ],
            [
                trans('texts.enter_credit'),
                function ($model) {
                    return \Illuminate\Support\Facades\URL::to("credits/create/{$model->public_id}");
                },
                function ($model) {
                    return \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_CREDIT);
                },
            ],
            [
                trans('texts.enter_expense'),
                function ($model) {
                    return \Illuminate\Support\Facades\URL::to("expenses/create/{$model->public_id}");
                },
                function ($model) {
                    return \Illuminate\Support\Facades\Auth::user()->can('create', ENTITY_EXPENSE);
                },
            ],
        ];
    }
}
