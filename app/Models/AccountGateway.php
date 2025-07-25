<?php

namespace App\Models;

use App\Libraries\Utils;
use DateTimeInterface;
use HTMLUtils;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Laracasts\Presenter\PresentableTrait;
use URL;

/**
 * Class AccountGateway.
 */
class AccountGateway extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\AccountGatewayPresenter';

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'config',
    ];

    /**
     * @param $provider
     *
     * @return string
     */
    public static function paymentDriverClass($provider)
    {
        $folder = 'App\\Ninja\\PaymentDrivers\\';
        $provider = str_replace('\\', '', $provider);
        $class = $folder . $provider . 'PaymentDriver';
        $class = str_replace('_', '', $class);

        if (class_exists($class)) {
            return $class;
        }

        return $folder . 'BasePaymentDriver';
    }

    public function getEntityType()
    {
        return ENTITY_ACCOUNT_GATEWAY;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gateway()
    {
        return $this->belongsTo('App\Models\Gateway');
    }

    /**
     * @return array
     */
    public function getCreditcardTypes()
    {
        $flags = unserialize(CREDIT_CARDS);
        $arrayOfImages = [];

        foreach ($flags as $card => $name) {
            if (($this->accepted_credit_cards & $card) == $card) {
                $arrayOfImages[] = ['source' => asset($name['card']), 'alt' => $name['text']];
            }
        }

        return $arrayOfImages;
    }

    /**
     * @param bool  $invitation
     * @param mixed $gatewayTypeId
     *
     * @return mixed
     */
    public function paymentDriver($invitation = false, $gatewayTypeId = false)
    {
        $class = static::paymentDriverClass($this->gateway->provider);

        return new $class($this, $invitation, $gatewayTypeId);
    }

    /**
     * @param $gatewayId
     *
     * @return bool
     */
    public function isGateway($gatewayId)
    {
        if (is_array($gatewayId)) {
            foreach ($gatewayId as $id) {
                if ($this->gateway_id == $id) {
                    return true;
                }
            }

            return false;
        }

        return $this->gateway_id == $gatewayId;
    }

    public function isCustom()
    {
        return in_array($this->gateway_id, [GATEWAY_CUSTOM1, GATEWAY_CUSTOM2, GATEWAY_CUSTOM3]);
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = Crypt::encrypt(json_encode($config));
    }

    public function getConfig()
    {
        return json_decode(Crypt::decrypt($this->config));
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    public function getConfigField($field)
    {
        return object_get($this->getConfig(), $field, false);
    }

    /**
     * @return bool|mixed
     */
    public function getPublishableKey()
    {
        if ( ! $this->isGateway([GATEWAY_STRIPE, GATEWAY_PAYMILL])) {
            return false;
        }

        return $this->getConfigField('publishableKey');
    }

    public function getAppleMerchantId()
    {
        if ( ! $this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('appleMerchantId');
    }

    /**
     * @return bool
     */
    public function getAchEnabled()
    {
        return ! empty($this->getConfigField('enableAch'));
    }

    /**
     * @return bool
     */
    public function getApplePayEnabled()
    {
        return ! empty($this->getConfigField('enableApplePay'));
    }

    /**
     * @return bool
     */
    public function getAlipayEnabled()
    {
        return ! empty($this->getConfigField('enableAlipay'));
    }

    /**
     * @return bool
     */
    public function getSofortEnabled()
    {
        return ! empty($this->getConfigField('enableSofort'));
    }

    /**
     * @return bool
     */
    public function getSepaEnabled()
    {
        return ! empty($this->getConfigField('enableSepa'));
    }

    /**
     * @return bool
     */
    public function getBitcoinEnabled()
    {
        return ! empty($this->getConfigField('enableBitcoin'));
    }

    /**
     * @return bool
     */
    public function getPayPalEnabled()
    {
        return ! empty($this->getConfigField('enablePayPal'));
    }

    /**
     * @return bool|mixed
     */
    public function getPlaidSecret()
    {
        if ( ! $this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('plaidSecret');
    }

    /**
     * @return bool|mixed
     */
    public function getPlaidClientId()
    {
        if ( ! $this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('plaidClientId');
    }

    /**
     * @return bool|mixed
     */
    public function getPlaidPublicKey()
    {
        if ( ! $this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('plaidPublicKey');
    }

    /**
     * @return bool
     */
    public function getPlaidEnabled()
    {
        return ! empty($this->getPlaidClientId()) && $this->getAchEnabled();
    }

    /**
     * @return null|string
     */
    public function getPlaidEnvironment()
    {
        if ( ! $this->getPlaidClientId()) {
            return;
        }

        $stripe_key = $this->getPublishableKey();

        return mb_substr(trim($stripe_key), 0, 8) == 'pk_test_' ? 'tartan' : 'production';
    }

    /**
     * @return string
     */
    public function getWebhookUrl()
    {
        $account = $this->account ? $this->account : Account::find($this->account_id);

        return URL::to(env('WEBHOOK_PREFIX', '') . 'payment_hook/' . $account->account_key . '/' . $this->gateway_id . env('WEBHOOK_SUFFIX', ''));
    }

    public function isTestMode()
    {
        if ($this->isGateway(GATEWAY_STRIPE)) {
            return str_contains($this->getPublishableKey(), 'test');
        }

        return $this->getConfigField('testMode');
    }

    public function getCustomHtml($invitation)
    {
        $text = $this->getConfigField('text');

        if ($text == strip_tags($text)) {
            $text = nl2br($text);
        }

        if (Utils::isNinja()) {
            $text = HTMLUtils::sanitizeHTML($text);
        }

        $templateService = app('App\Services\TemplateService');
        $text = $templateService->processVariables($text, ['invitation' => $invitation]);

        return $text;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
