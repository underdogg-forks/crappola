<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Queue\SerializesModels;

/**
 * Class PaymentWasVoided.
 */
class PaymentWasVoided extends Event
{
    use SerializesModels;

    /**
     * @var Payment
     */
    public $payment;

    /**
     * Create a new event instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
}
