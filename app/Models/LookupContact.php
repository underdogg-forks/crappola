<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;

/**
 * Class ExpenseCategory.
 *
 * @property int           $id
 * @property int           $lookup_account_id
 * @property string        $contact_key
 * @property LookupAccount $lookupAccount
 *
 * @method static Builder|LookupContact newModelQuery()
 * @method static Builder|LookupContact newQuery()
 * @method static Builder|LookupContact query()
 * @method static Builder|LookupContact whereContactKey($value)
 * @method static Builder|LookupContact whereId($value)
 * @method static Builder|LookupContact whereLookupAccountId($value)
 *
 * @mixin \Eloquent
 */
class LookupContact extends LookupModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'lookup_account_id',
        'contact_key',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
