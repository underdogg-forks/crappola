<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;

/**
 * Class ExpenseCategory.
 *
 * @property int           $id
 * @property int           $lookup_account_id
 * @property string        $token
 * @property LookupAccount $lookupAccount
 *
 * @method static Builder|LookupAccountToken newModelQuery()
 * @method static Builder|LookupAccountToken newQuery()
 * @method static Builder|LookupAccountToken query()
 * @method static Builder|LookupAccountToken whereId($value)
 * @method static Builder|LookupAccountToken whereLookupAccountId($value)
 * @method static Builder|LookupAccountToken whereToken($value)
 *
 * @mixin \Eloquent
 */
class LookupAccountToken extends LookupModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'lookup_account_id',
        'token',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
