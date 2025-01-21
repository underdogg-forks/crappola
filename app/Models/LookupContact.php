<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

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
}
