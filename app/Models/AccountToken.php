<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AccountToken.
 */
class AccountToken extends EntityModel
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function getEntityType()
    {
        return ENTITY_TOKEN;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

AccountToken::creating(function ($token) {
    LookupAccountToken::createNew($token->account->account_key, [
        'token' => $token->token,
    ]);
});

AccountToken::deleted(function ($token) {
    if ($token->forceDeleting) {
        LookupAccountToken::deleteWhere([
            'token' => $token->token,
        ]);
    }
});
