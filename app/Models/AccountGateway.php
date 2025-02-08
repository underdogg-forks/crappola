<?php

namespace App\Models;

use DateTimeInterface;
use Utils;
use HTMLUtils;
use Crypt;
use HTMLUtils;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
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

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $hidden = [
        'config',
    ];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_ACCOUNT_GATEWAY;
    }

    /**
     * @return BelongsTo
     */
    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    /**
     * @return array<int, array{source: string, alt: mixed}>
     */
    public function getCreditcardTypes(): array
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

    public function isCustom(): bool
    {
        return in_array($this->gateway_id, [GATEWAY_CUSTOM1, GATEWAY_CUSTOM2, GATEWAY_CUSTOM3]);
    }

    public function setConfig($config): void
    {
        $this->config = Crypt::encrypt(json_encode($config));
    }

    public function getAppleMerchantId()
    {
        if (! $this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('appleMerchantId');
    }

    public function isGateway($gatewayId): bool
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

    /**
     * @return mixed
     */
    public function getConfigField($field)
    {
        return object_get($this->getConfig(), $field, false);
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return json_decode(Crypt::decrypt($this->config));
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
        if (! $this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('plaidSecret');
    }

    /**
     * @return bool|mixed
     */
    public function getPlaidPublicKey()
    {
        if (! $this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('plaidPublicKey');
    }

    public function getPlaidEnabled(): bool
    {
        return ! empty($this->getPlaidClientId()) && $this->getAchEnabled();
    }

    /**
     * @return bool|mixed
     */
    public function getPlaidClientId()
    {
        if (! $this->isGateway(GATEWAY_STRIPE)) {
            return false;
        }

        return $this->getConfigField('plaidClientId');
    }

    /**
     * @return bool
     */
    public function getAchEnabled()
    {
        return ! empty($this->getConfigField('enableAch'));
    }

    /**
     * @return null|string
     */
    public function getPlaidEnvironment()
    {
        if (! $this->getPlaidClientId()) {
            return;
        }

        $stripe_key = $this->getPublishableKey();

        return substr(trim($stripe_key), 0, 8) == 'pk_test_' ? 'tartan' : 'production';
    }

    /**
     * @return bool|mixed
     */
    public function getPublishableKey()
    {
        if (! $this->isGateway([GATEWAY_STRIPE, GATEWAY_PAYMILL])) {
            return false;
        }

        return $this->getConfigField('publishableKey');
    }

    /**
     * @return string
     */
    public function getWebhookUrl()
    {
        $company = $this->company ? $this->company : Company::find($this->company_id);

        return URL::to(env('WEBHOOK_PREFIX', '') . 'payment_hook/' . $company->account_key . '/' . $this->gateway_id . env('WEBHOOK_SUFFIX', ''));
    }

    public function isTestMode()
    {
        if ($this->isGateway(GATEWAY_STRIPE)) {
            return strpos($this->getPublishableKey(), 'test') !== false;
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

        $templateService = app(TemplateService::class);

        return $templateService->processVariables($text, ['invitation' => $invitation]);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
