<?php

namespace App\Models;

/**
 * Class ExpenseCategory.
 *
 * @property int                       $id
 * @property int                       $lookup_account_id
 * @property string                    $contact_key
 * @property \App\Models\LookupAccount $lookupAccount
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LookupContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupContact query()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupContact whereContactKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupContact whereLookupAccountId($value)
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
