<?php

namespace App\Ninja\Datatables;

class PaymentTermDatatable extends EntityDatatable
{
    public $entityType = ENTITY_PAYMENT_TERM;

    public $sortCol = 1;

    public function columns(): array
    {
        return [
            [
                'num_days',
                fn ($model) => link_to("payment_terms/{$model->public_id}/edit", trans('texts.payment_terms_net') . ' ' . ($model->num_days == -1 ? 0 : $model->num_days))->toHtml(),
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_payment_term'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("payment_terms/{$model->public_id}/edit"),
            ],
        ];
    }
}
