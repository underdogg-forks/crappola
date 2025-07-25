<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class PaymentType.
 */
class PaymentType extends Eloquent
{
    public $timestamps = false;

    public $guarded = [];

    public static function parseCardType($cardName)
    {
        $cardTypes = [
            'visa'            => PAYMENT_TYPE_VISA,
            'americanexpress' => PAYMENT_TYPE_AMERICAN_EXPRESS,
            'amex'            => PAYMENT_TYPE_AMERICAN_EXPRESS,
            'mastercard'      => PAYMENT_TYPE_MASTERCARD,
            'discover'        => PAYMENT_TYPE_DISCOVER,
            'jcb'             => PAYMENT_TYPE_JCB,
            'dinersclub'      => PAYMENT_TYPE_DINERS,
            'carteblanche'    => PAYMENT_TYPE_CARTE_BLANCHE,
            'chinaunionpay'   => PAYMENT_TYPE_UNIONPAY,
            'unionpay'        => PAYMENT_TYPE_UNIONPAY,
            'laser'           => PAYMENT_TYPE_LASER,
            'maestro'         => PAYMENT_TYPE_MAESTRO,
            'solo'            => PAYMENT_TYPE_SOLO,
            'switch'          => PAYMENT_TYPE_SWITCH,
        ];

        $cardName = mb_strtolower(str_replace([' ', '-', '_'], '', $cardName));

        if (empty($cardTypes[$cardName]) && 1 == preg_match('/^(' . implode('|', array_keys($cardTypes)) . ')/', $cardName, $matches)) {
            // Some gateways return extra stuff after the card name
            $cardName = $matches[1];
        }

        if ( ! empty($cardTypes[$cardName])) {
            return $cardTypes[$cardName];
        }

        return PAYMENT_TYPE_CREDIT_CARD_OTHER;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gatewayType()
    {
        return $this->belongsTo('App\Models\GatewayType');
    }
}
