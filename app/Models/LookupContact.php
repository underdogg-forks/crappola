<?php
namespace App\Models;

use DateTimeInterface;
use Eloquent;

/**
 * Class ExpenseCategory.
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
