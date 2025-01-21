<?php

namespace App\Ninja\PaymentDrivers;

class PayPalProPaymentDriver extends BasePaymentDriver
{
    public function gatewayTypes(): array
    {
        return [
            GATEWAY_TYPE_CREDIT_CARD,
        ];
    }

    protected function paymentDetails($paymentMethod = false): array
    {
        $data = parent::paymentDetails();

        $data['ButtonSource'] = 'InvoiceNinja_SP';

        return $data;
    }
}
