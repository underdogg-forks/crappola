<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;

/**
 * Class PaymentLibrary.
 */
class PaymentLibrary extends Model
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
     * @return HasMany
     */
    public function gateways()
    {
        return $this->hasMany(Gateway::class, 'payment_library_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
