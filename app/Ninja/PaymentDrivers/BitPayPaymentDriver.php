<?php

namespace App\Ninja\PaymentDrivers;

class BitPayPaymentDriver extends BasePaymentDriver
{
    public function gatewayTypes(): array
    {
        return [
            GATEWAY_TYPE_BITCOIN,
        ];
    }
}
