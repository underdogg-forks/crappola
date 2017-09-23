<?php
namespace App\Models;

use Eloquent;

/**
 * Class PaymentStatus.
 */
class PaymentStatus extends Eloquent
{
    public $table = 'lookup__paymentstatuses';
    /**
     * @var bool
     */
    public $timestamps = false;
}
