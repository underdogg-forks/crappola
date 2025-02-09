<?php

namespace App\Models;

use DateTimeInterface;

/**
 * Class ExpenseCategory.
 */
class LookupAccountToken extends LookupModel
{
    protected $fillable = [
        'lookup_account_id',
        'token',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
