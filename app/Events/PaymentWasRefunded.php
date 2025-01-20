<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Queue\SerializesModels;

/**
 * Class PaymentWasRefunded.
 */
class PaymentWasRefunded extends Event
{
    use SerializesModels;

    /**
     * @var Payment
     */
    public $payment;

    public $refundAmount;

    /**
     * Create a new event instance.
     */
    public function __construct(Payment $payment, $refundAmount)
    {
        $this->payment = $payment;
        $this->refundAmount = $refundAmount;
    }
}
