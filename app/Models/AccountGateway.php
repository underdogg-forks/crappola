<?php

namespace App\Models;

use HTMLUtils;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Utils;

/**
 * Class AccountGateway.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property int                             $gateway_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string                          $config
 * @property int                             $public_id
 * @property int|null                        $accepted_credit_cards
 * @property int|null                        $show_address
 * @property int|null                        $update_address
 * @property int|null                        $require_cvv
 * @property int|null                        $show_shipping_address
 * @property \App\Models\Gateway             $gateway
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereAcceptedCreditCards($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereRequireCvv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereShowAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereShowShippingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereUpdateAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGateway withoutTrashed()
 *
 * @mixin \Eloquent
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
    protected $hidden = [
        'config',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @param $provider
     *
     * @return string
     */
    public static function paymentDriverClass($provider): string
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

    public function getEntityType(): string
    {
        return ENTITY_ACCOUNT_GATEWAY;
    }

    public function gateway()
    {
        return $this->belongsTo(\App\Models\Gateway::class);
    }

    /**
     * @return array
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
     * @param $gatewayId
     *
     * @return bool
     */
    public function isGateway($gatewayId)
    {
        if (is_array($gatewayId)) {
            return in_array($this->gateway_id, $gatewayId);
        }

        return $this->gateway_id == $gatewayId;
    }

    public function isCustom(): bool
    {
        return in_array($this->gateway_id, [GATEWAY_CUSTOM1, GATEWAY_CUSTOM2, GATEWAY_CUSTOM3]);
    }

    /**
     * @param $config
     */
    public function setConfig($config): void
    {
        $this->config = \Illuminate\Support\Facades\Crypt::encrypt(json_encode($config));
    }

    public function getConfig(): mixed
    {
        return json_decode(\Illuminate\Support\Facades\Crypt::decrypt($this->config));
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
    public function getAchEnabled(): bool
    {
        return ! empty($this->getConfigField('enableAch'));
    }

    /**
     * @return bool
     */
    public function getApplePayEnabled(): bool
    {
        return ! empty($this->getConfigField('enableApplePay'));
    }

    /**
     * @return bool
     */
    public function getAlipayEnabled(): bool
    {
        return ! empty($this->getConfigField('enableAlipay'));
    }

    /**
     * @return bool
     */
    public function getSofortEnabled(): bool
    {
        return ! empty($this->getConfigField('enableSofort'));
    }

    /**
     * @return bool
     */
    public function getSepaEnabled(): bool
    {
        return ! empty($this->getConfigField('enableSepa'));
    }

    /**
     * @return bool
     */
    public function getBitcoinEnabled(): bool
    {
        return ! empty($this->getConfigField('enableBitcoin'));
    }

    /**
     * @return bool
     */
    public function getPayPalEnabled(): bool
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
    public function getPlaidEnabled(): bool
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

        return mb_substr(trim($stripe_key), 0, 8) === 'pk_test_' ? 'tartan' : 'production';
    }

    /**
     * @return string
     */
    public function getWebhookUrl()
    {
        $account = $this->account ?: Account::find($this->account_id);

        return \Illuminate\Support\Facades\URL::to(env('WEBHOOK_PREFIX', '') . 'payment_hook/' . $account->account_key . '/' . $this->gateway_id . env('WEBHOOK_SUFFIX', ''));
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

        $templateService = app(\App\Services\TemplateService::class);
        $text = $templateService->processVariables($text, ['invitation' => $invitation]);

        return $text;
    }
}
