<?php
namespace App\Models;

use Eloquent;

/**
 * Class PaymentStatus.
 */
class PaymentStatus extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'lookup__paymentstatuses';
    /**
     * @var bool
     */
    public $timestamps = false;
}
