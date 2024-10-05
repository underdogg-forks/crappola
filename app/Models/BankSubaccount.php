<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankSubaccount.
 */
class BankSubaccount extends EntityModel
{
    use SoftDeletes;

    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_BANK_SUBACCOUNT;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank_account()
    {
        return $this->belongsTo(\App\Models\BankAccount::class);
    }
}
