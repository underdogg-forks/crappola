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
                fn ($model) => link_to(sprintf('bank_accounts/%s/edit', $model->public_id), $model->bank_name)->toHtml(),
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
                fn ($model) => \Illuminate\Support\Facades\URL::to(sprintf('bank_accounts/%s/edit', $model->public_id)),
            ],
        ];
    }
}
