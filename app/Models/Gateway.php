<?php

namespace App\Models;

use Omnipay;
use Utils;

/**
 * Class Gateway.
 *
 * @property int                             $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string                          $name
 * @property string                          $provider
 * @property int                             $visible
 * @property int                             $payment_library_id
 * @property int                             $sort_order
 * @property int                             $recommended
 * @property string|null                     $site_url
 * @property int                             $is_offsite
 * @property int                             $is_secure
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway primary($accountGatewaysIds)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway query()
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway secondary($accountGatewaysIds)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereIsOffsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereIsSecure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway wherePaymentLibraryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereRecommended($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereSiteUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Gateway whereVisible($value)
 *
 * @mixin \Eloquent
 */
class Gateway extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    public static $gatewayTypes = [
        GATEWAY_TYPE_CREDIT_CARD,
        GATEWAY_TYPE_BANK_TRANSFER,
        GATEWAY_TYPE_PAYPAL,
        GATEWAY_TYPE_BITCOIN,
        GATEWAY_TYPE_DWOLLA,
        GATEWAY_TYPE_TOKEN,
        GATEWAY_TYPE_GOCARDLESS,
    ];

    // these will appear in the primary gateway select
    // the rest are shown when selecting 'more options'
    /**
     * @var array
     */
    public static $preferred = [
        GATEWAY_PAYPAL_EXPRESS,
        GATEWAY_STRIPE,
        GATEWAY_WEPAY,
        GATEWAY_BRAINTREE,
        GATEWAY_AUTHORIZE_NET,
        GATEWAY_MOLLIE,
        GATEWAY_GOCARDLESS,
        GATEWAY_BITPAY,
        GATEWAY_DWOLLA,
        GATEWAY_CUSTOM1,
        GATEWAY_CUSTOM2,
        GATEWAY_CUSTOM3,
    ];

    // allow adding these gateway if another gateway
    // is already configured
    /**
     * @var array
     */
    public static $alternate = [
        GATEWAY_PAYPAL_EXPRESS,
        GATEWAY_BITPAY,
        GATEWAY_DWOLLA,
        GATEWAY_CUSTOM1,
        GATEWAY_CUSTOM2,
        GATEWAY_CUSTOM3,
    ];

    /**
     * @var array
     */
    public static $hiddenFields = [
        // PayPal
        'headerImageUrl',
        'solutionType',
        'landingPage',
        'brandName',
        'logoImageUrl',
        'borderColor',
        // Dwolla
        'returnUrl',
    ];

    /**
     * @var array
     */
    public static $optionalFields = [
        // PayPal
        'testMode',
        'developerMode',
        // Dwolla
        'sandbox',
        // Payfast
        'pdtKey',
        'passphrase',
        // Realex
        '3dSecure',
        // WorldPay
        'callbackPassword',
        'secretWord',
    ];

    protected $fillable = [
        'provider',
        'is_offsite',
        'sort_order',
    ];

    /**
     * @param $type
     *
     * @return string
     */
    public static function getPaymentTypeName($type)
    {
        return Utils::toCamelCase(mb_strtolower(str_replace('PAYMENT_TYPE_', '', $type)));
    }

    /**
     * @param $gatewayIds
     *
     * @return int
     */
    public static function hasStandardGateway($gatewayIds): int
    {
        $diff = array_diff($gatewayIds, static::$alternate);

        return count($diff);
    }

    /**
     * @return string
     */
    public function getLogoUrl(): string
    {
        return '/images/gateways/logo_' . $this->provider . '.png';
    }

    /**
     * @param $gatewayId
     *
     * @return bool
     */
    public function isGateway($gatewayId): bool
    {
        return $this->id == $gatewayId;
    }

    /**
     * @param $query
     * @param $accountGatewaysIds
     */
    public function scopePrimary($query, $accountGatewaysIds): void
    {
        $query->where('payment_library_id', '=', 1)
            ->whereIn('id', static::$preferred)
            ->whereIn('id', $accountGatewaysIds);

        if ( ! Utils::isNinja()) {
            $query->where('id', '!=', GATEWAY_WEPAY);
        }
    }

    /**
     * @param $query
     * @param $accountGatewaysIds
     */
    public function scopeSecondary($query, $accountGatewaysIds): void
    {
        $query->where('payment_library_id', '=', 1)
            ->whereNotIn('id', static::$preferred)
            ->whereIn('id', $accountGatewaysIds);
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function getHelp()
    {
        $link = '';

        if ($this->id == GATEWAY_AUTHORIZE_NET) {
            $link = 'http://reseller.authorize.net/application/?id=5560364';
        } elseif ($this->id == GATEWAY_PAYPAL_EXPRESS) {
            $link = 'https://www.paypal.com/us/cgi-bin/webscr?cmd=_login-api-run';
        } elseif ($this->id == GATEWAY_TWO_CHECKOUT) {
            $link = 'https://www.2checkout.com/referral?r=2c37ac2298';
        } elseif ($this->id == GATEWAY_BITPAY) {
            $link = 'https://bitpay.com/dashboard/signup';
        } elseif ($this->id == GATEWAY_DWOLLA) {
            $link = 'https://www.dwolla.com/register';
        } elseif ($this->id == GATEWAY_SAGE_PAY_DIRECT || $this->id == GATEWAY_SAGE_PAY_SERVER) {
            $link = 'https://applications.sagepay.com/apply/2C02C252-0F8A-1B84-E10D-CF933EFCAA99';
        } elseif ($this->id == GATEWAY_STRIPE) {
            $link = 'https://dashboard.stripe.com/account/apikeys';
        } elseif ($this->id == GATEWAY_WEPAY) {
            $link = url('/gateways/create?wepay=true');
        }

        $key = 'texts.gateway_help_' . $this->id;
        $str = trans($key, [
            'link'          => sprintf("<a href='%s' >Click here</a>", $link),
            'complete_link' => url('/complete'),
        ]);

        return $key != $str ? $str : '';
    }

    public function getFields()
    {
        if ($this->isCustom()) {
            return [
                'name' => '',
                'text' => '',
            ];
        }

        return Omnipay::create($this->provider)->getDefaultParameters();
    }

    public function isCustom(): bool
    {
        return in_array($this->id, [GATEWAY_CUSTOM1, GATEWAY_CUSTOM2, GATEWAY_CUSTOM3]);
    }
}
