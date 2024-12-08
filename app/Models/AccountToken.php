<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class AccountToken.
 *
 * @property int         $id
 * @property int         $account_id
 * @property int         $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $name
 * @property string      $token
 * @property int|null    $public_id
 * @property Account     $account
 * @property User        $user
 *
 * @method static Builder|AccountToken newModelQuery()
 * @method static Builder|AccountToken newQuery()
 * @method static Builder|AccountToken onlyTrashed()
 * @method static Builder|AccountToken query()
 * @method static Builder|AccountToken scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|AccountToken whereAccountId($value)
 * @method static Builder|AccountToken whereCreatedAt($value)
 * @method static Builder|AccountToken whereDeletedAt($value)
 * @method static Builder|AccountToken whereId($value)
 * @method static Builder|AccountToken whereName($value)
 * @method static Builder|AccountToken wherePublicId($value)
 * @method static Builder|AccountToken whereToken($value)
 * @method static Builder|AccountToken whereUpdatedAt($value)
 * @method static Builder|AccountToken whereUserId($value)
 * @method static Builder|AccountToken withActiveOrSelected($id = false)
 * @method static Builder|AccountToken withArchived()
 * @method static Builder|AccountToken withTrashed()
 * @method static Builder|AccountToken withoutTrashed()
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
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
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
