<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;

/**
 * Class PaymentLibrary.
 */
class PaymentLibrary extends Eloquent
{
    /**
     * @var string
     */
    protected $table = 'payment_libraries';
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gateways()
    {
        return $this->hasMany('App\Models\Gateway', 'payment_library_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
