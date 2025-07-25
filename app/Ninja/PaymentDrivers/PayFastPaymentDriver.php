<?php

namespace App\Ninja\PaymentDrivers;

use Illuminate\Support\Facades\Request;

class PayFastPaymentDriver extends BasePaymentDriver
{
    protected $transactionReferenceParam = 'm_payment_id';

    public function completeOffsitePurchase($input)
    {
        parent::completeOffsitePurchase([
            'token' => Request::query('pt'),
        ]);
    }

    protected function paymentDetails($paymentMethod = false)
    {
        $data = parent::paymentDetails();
        $data['notifyUrl'] = $this->invitation->getLink('complete', true);

        return $data;
    }
}
