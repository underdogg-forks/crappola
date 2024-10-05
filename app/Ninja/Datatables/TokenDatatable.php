<?php

namespace App\Ninja\Datatables;

class TokenDatatable extends EntityDatatable
{
    public $entityType = ENTITY_TOKEN;

    public function columns(): array
    {
        return [
            [
                'name',
                fn ($model) => link_to("tokens/{$model->public_id}/edit", $model->name)->toHtml(),
            ],
            [
                'token',
                fn ($model) => $model->token,
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                uctrans('texts.edit_token'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("tokens/{$model->public_id}/edit"),
            ],
        ];
    }
}
