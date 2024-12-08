<?php

namespace App\Models;

/**
 * Class ExpenseCategory.
 *
 * @property int                       $id
 * @property int                       $lookup_account_id
 * @property string                    $token
 * @property \App\Models\LookupAccount $lookupAccount
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LookupAccountToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupAccountToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupAccountToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|LookupAccountToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupAccountToken whereLookupAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LookupAccountToken whereToken($value)
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
}
