<?php

namespace App\Models;

/**
 * Class PaymentLibrary.
 */
class PaymentLibrary extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string
     */
    protected $table = 'payment_libraries';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gateways()
    {
        return $this->hasMany(\App\Models\Gateway::class, 'payment_library_id');
    }
}
