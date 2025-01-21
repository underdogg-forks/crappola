<?php

namespace App\Ninja\Datatables;

use URL;

class BankAccountDatatable extends EntityDatatable
{
    public $entityType = ENTITY_BANK_ACCOUNT;

    public function columns(): array
    {
        return [
            [
                'bank_name',
                function ($model): string {
                    return link_to("bank_accounts/{$model->public_id}/edit", $model->bank_name)->toHtml();
                },
            ],
            [
                'bank_library_id',
                function ($model): string {
                    return 'OFX';
                },
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                uctrans('texts.edit_bank_account'),
                function ($model) {
                    return URL::to("bank_accounts/{$model->public_id}/edit");
                },
            ],
        ];
    }
}
