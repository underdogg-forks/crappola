<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\URL;

class TaxRateDatatable extends EntityDatatable
{
    public $entityType = ENTITY_TAX_RATE;

    public function columns(): array
    {
        return [
            [
                'name',
                fn ($model) => link_to(sprintf('tax_rates/%s/edit', $model->public_id), $model->name)->toHtml(),
            ],
            [
                'rate',
                fn ($model): string => ($model->rate + 0) . '%',
            ],
            [
                'type',
                function ($model) {
                    if (auth()->user()->account->inclusive_taxes) {
                        return trans('texts.inclusive');
                    }

                    return $model->is_inclusive ? trans('texts.inclusive') : trans('texts.exclusive');
                },
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                uctrans('texts.edit_tax_rate'),
                fn ($model) => URL::to(sprintf('tax_rates/%s/edit', $model->public_id)),
            ],
        ];
    }
}
