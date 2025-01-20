<?php

namespace App\Ninja\PaymentDrivers;

class Custom2PaymentDriver extends BasePaymentDriver
{
    public function gatewayTypes(): array
    {
        return [
            GATEWAY_TYPE_CUSTOM2,
        ];
    }
}
