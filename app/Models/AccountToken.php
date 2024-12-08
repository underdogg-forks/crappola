<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AccountToken.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null                     $name
 * @property string                          $token
 * @property int|null                        $public_id
 * @property \App\Models\Account             $account
 * @property \App\Models\User                $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountToken withoutTrashed()
 *
 * @mixin \Eloquent
 */
class AccountToken extends EntityModel
{
    use SoftDeletes;

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_TOKEN;
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }
}

AccountToken::creating(function ($token): void {
    LookupAccountToken::createNew($token->account->account_key, [
        'token' => $token->token,
    ]);
});

AccountToken::deleted(function ($token): void {
    if ($token->forceDeleting) {
        LookupAccountToken::deleteWhere([
            'token' => $token->token,
        ]);
    }
});
