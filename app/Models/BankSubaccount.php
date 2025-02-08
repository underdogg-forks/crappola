<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankSubaccount.
 */
class BankSubaccount extends EntityModel
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
        return ENTITY_BANK_SUBACCOUNT;
    }

    /**
     * @return BelongsTo
     */
    public function bank_account()
    {
        return $this->belongsTo(BankAccount::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
