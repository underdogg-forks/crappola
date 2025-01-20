<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->hasMany('App\Models\Gateway', 'payment_library_id');
    }
}
