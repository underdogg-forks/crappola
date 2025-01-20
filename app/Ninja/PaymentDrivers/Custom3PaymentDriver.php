<?php

namespace App\Ninja\PaymentDrivers;

class Custom3PaymentDriver extends BasePaymentDriver
{
    public function gatewayTypes(): array
    {
        return [
            GATEWAY_TYPE_CUSTOM3,
        ];
    }
}
