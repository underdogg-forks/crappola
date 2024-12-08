<?php

namespace App\Models;

/**
 * Class PaymentType.
 *
 * @property int                          $id
 * @property string                       $name
 * @property int|null                     $gateway_type_id
 * @property \App\Models\GatewayType|null $gatewayType
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentType query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentType whereGatewayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentType whereName($value)
 *
 * @mixin \Eloquent
 */
class PaymentType extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public static function parseCardType($cardName): int
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

        if (isset($cardTypes[$cardName]) && $cardTypes[$cardName] !== 0) {
            return $cardTypes[$cardName];
        }

        return PAYMENT_TYPE_CREDIT_CARD_OTHER;
    }

    public function gatewayType()
    {
        return $this->belongsTo(\App\Models\GatewayType::class);
    }
}
