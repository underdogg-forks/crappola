<?php

namespace App\Ninja\PaymentDrivers;

class Custom1PaymentDriver extends BasePaymentDriver
{
    public function gatewayTypes(): array
    {
        return [
            GATEWAY_TYPE_CUSTOM1,
        ];
    }
}
