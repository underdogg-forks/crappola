<?php

namespace App\Ninja\PaymentDrivers;

class PaymentExpressPxPayPaymentDriver extends BasePaymentDriver
{
    protected function paymentDetails($paymentMethod = false)
    {
        $data = parent::paymentDetails();

        $data['transactionId'] = mb_substr($data['transactionId'] . '-' . strrev($this->invoice()->updated_at->timestamp), 0, 15);

        return $data;
    }
}
