<?php

namespace App\Ninja\Datatables;

class UserDatatable extends EntityDatatable
{
    public $entityType = ENTITY_USER;

    public function columns(): array
    {
        return [
            [
                'first_name',
                fn ($model) => $model->public_id ? link_to('users/' . $model->public_id . '/edit', $model->first_name . ' ' . $model->last_name)->toHtml() : e($model->first_name . ' ' . $model->last_name),
            ],
            [
                'email',
                fn ($model) => $model->email,
            ],
            [
                'confirmed',
                function ($model) {
                    if ( ! $model->public_id) {
                        return self::getStatusLabel(USER_STATE_OWNER);
                    }
                    if ($model->deleted_at) {
                        return self::getStatusLabel(USER_STATE_DISABLED);
                    }
                    if ($model->confirmed) {
                        if ($model->is_admin) {
                            return self::getStatusLabel(USER_STATE_ADMIN);
                        }

                        return self::getStatusLabel(USER_STATE_ACTIVE);
                    }

                    return self::getStatusLabel(USER_STATE_PENDING);
                },
            ],
        ];
    }

    public function actions(): array
    {
        return [
            [
                uctrans('texts.edit_user'),
                fn ($model) => \Illuminate\Support\Facades\URL::to("users/{$model->public_id}/edit"),
                fn ($model) => $model->public_id,
            ],
            [
                uctrans('texts.send_invite'),
                fn ($model)       => \Illuminate\Support\Facades\URL::to("send_confirmation/{$model->public_id}"),
                fn ($model): bool => $model->public_id && ! $model->confirmed,
            ],
        ];
    }

    private function getStatusLabel(string $state): string
    {
        $label = trans("texts.{$state}");
        $class = 'default';
        switch ($state) {
            case USER_STATE_PENDING:
                $class = 'default';
                break;
            case USER_STATE_ACTIVE:
                $class = 'info';
                break;
            case USER_STATE_DISABLED:
                $class = 'warning';
                break;
            case USER_STATE_OWNER:
                $class = 'success';
                break;
            case USER_STATE_ADMIN:
                $class = 'primary';
                break;
        }

        return "<h4><div class=\"label label-{$class}\">{$label}</div></h4>";
    }
}
