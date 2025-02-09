<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class PaymentLibrary.
 */
class PaymentLibrary extends Eloquent
{
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
        return $this->hasMany('App\Models\Gateway', 'payment_library_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
