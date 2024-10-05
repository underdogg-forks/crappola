<?php

namespace App\Ninja\Datatables;

class BankAccountDatatable extends EntityDatatable
{
    public $entityType = ENTITY_BANK_ACCOUNT;

    public function columns(): array
    {
        return [
            [
                'bank_name',
                fn ($model) => link_to("bank_accounts/{$model->public_id}/edit", $model->bank_name)->toHtml(),
            ],
            [
                'bank_library_id',
                fn ($model): string => 'OFX',
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                uctrans('texts.edit_bank_account'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("bank_accounts/{$model->public_id}/edit"),
            ],
        ];
    }
}
