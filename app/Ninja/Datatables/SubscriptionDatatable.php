<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\URL;

class SubscriptionDatatable extends EntityDatatable
{
    public $entityType = ENTITY_SUBSCRIPTION;

    public function columns(): array
    {
        return [
            [
                'event',
                fn ($model) => trans('texts.subscription_event_' . $model->event),
            ],
            [
                'target',
                fn ($model) => $this->showWithTooltip($model->target, 40),
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                uctrans('texts.edit_subscription'),
                fn ($model) => URL::to(sprintf('subscriptions/%s/edit', $model->public_id)),
            ],
        ];
    }
}
