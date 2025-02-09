<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class VendorDatatable extends EntityDatatable
{
    public $entityType = ENTITY_VENDOR;

    public $sortCol = 4;

    public function columns(): array
    {
        return [
            [
                'name',
                function ($model): string {
                    $str = link_to('vendors/' . $model->public_id, $model->name ?: '')->toHtml();

                    return $this->addNote($str, $model->private_notes);
                },
            ],
            [
                'city',
                fn ($model) => $model->city,
            ],
            [
                'work_phone',
                fn ($model) => $model->work_phone,
            ],
            [
                'email',
                fn ($model) => link_to('vendors/' . $model->public_id, $model->email ?: '')->toHtml(),
            ],
            [
                'created_at',
                fn ($model) => Utils::timestampToDateString(strtotime($model->created_at)),
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                trans('texts.edit_vendor'),
                fn ($model) => URL::to(sprintf('vendors/%s/edit', $model->public_id)),
                fn ($model) => Auth::user()->can('view', [ENTITY_VENDOR, $model]),
            ],
            [
                '--divider--', fn (): bool => false,
                fn ($model): bool => Auth::user()->can('edit', [ENTITY_VENDOR, $model]) && Auth::user()->can('create', ENTITY_EXPENSE),
            ],
            [
                trans('texts.enter_expense'),
                fn ($model) => URL::to('expenses/create/0/' . $model->public_id),
                fn ($model) => Auth::user()->can('create', ENTITY_EXPENSE),
            ],
        ];
    }
}
