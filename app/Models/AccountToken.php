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

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_TOKEN;
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

AccountToken::creating(function ($token): void {
    LookupAccountToken::createNew($token->company->account_key, [
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
