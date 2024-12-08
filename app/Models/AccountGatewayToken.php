<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AccountGatewayToken.
 *
 * @property int                                                                      $id
 * @property int                                                                      $account_id
 * @property int                                                                      $contact_id
 * @property int                                                                      $account_gateway_id
 * @property int                                                                      $client_id
 * @property string                                                                   $token
 * @property \Illuminate\Support\Carbon|null                                          $created_at
 * @property \Illuminate\Support\Carbon|null                                          $updated_at
 * @property \Illuminate\Support\Carbon|null                                          $deleted_at
 * @property int|null                                                                 $default_payment_method_id
 * @property \App\Models\AccountGateway                                               $account_gateway
 * @property \App\Models\Contact                                                      $contact
 * @property \App\Models\PaymentMethod|null                                           $default_payment_method
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\PaymentMethod> $payment_methods
 * @property int|null                                                                 $payment_methods_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken clientAndGateway($clientId, $accountGatewayId)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereAccountGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereDefaultPaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewayToken withoutTrashed()
 *
 * @mixin \Eloquent
 */
class AccountGatewayToken extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'account_gateway_id',
        'client_id',
        'token',
    ];

    public function payment_methods()
    {
        return $this->hasMany(\App\Models\PaymentMethod::class);
    }

    public function account_gateway()
    {
        return $this->belongsTo(\App\Models\AccountGateway::class);
    }

    public function contact()
    {
        return $this->belongsTo(\App\Models\Contact::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function default_payment_method()
    {
        return $this->hasOne(\App\Models\PaymentMethod::class, 'id', 'default_payment_method_id');
    }

    public function getEntityType(): string
    {
        return ENTITY_CUSTOMER;
    }

    public function autoBillLater()
    {
        if ($this->default_payment_method) {
            return $this->default_payment_method->requiresDelayedAutoBill();
        }

        return false;
    }

    /**
     * @param $query
     * @param $clientId
     * @param $accountGatewayId
     *
     * @return mixed
     */
    public function scopeClientAndGateway($query, $clientId, $accountGatewayId)
    {
        $query->where('client_id', '=', $clientId)
            ->where('account_gateway_id', '=', $accountGatewayId);

        return $query;
    }

    public function gatewayName()
    {
        return $this->account_gateway->gateway->name;
    }

    /**
     * @return bool|string
     */
    public function gatewayLink(): string|false
    {
        $accountGateway = $this->account_gateway;

        if ($accountGateway->gateway_id == GATEWAY_STRIPE) {
            return 'https://dashboard.stripe.com/customers/' . $this->token;
        }

        if ($accountGateway->gateway_id == GATEWAY_BRAINTREE) {
            $merchantId = $accountGateway->getConfigField('merchantId');
            $testMode = $accountGateway->getConfigField('testMode');

            return $testMode ? sprintf('https://sandbox.braintreegateway.com/merchants/%s/customers/%s', $merchantId, $this->token) : sprintf('https://www.braintreegateway.com/merchants/%s/customers/%s', $merchantId, $this->token);
        }

        if ($accountGateway->gateway_id == GATEWAY_GOCARDLESS) {
            $testMode = $accountGateway->getConfigField('testMode');

            return $testMode ? 'https://manage-sandbox.gocardless.com/customers/' . $this->token : 'https://manage.gocardless.com/customers/' . $this->token;
        }

        return false;
    }
}
