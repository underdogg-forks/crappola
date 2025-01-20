<?php

namespace App\Ninja\PaymentDrivers;

class PagSeguroPaymentDriver extends BasePaymentDriver
{
    protected function paymentDetails($paymentMethod = false): array
    {
        $data = parent::paymentDetails($paymentMethod);
        $data['transactionReference'] = $this->invoice()->invoice_number;

        return $data;
    }
}
