<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class AccountGatewayToken.
 *
 * @property int                            $id
 * @property int                            $account_id
 * @property int                            $contact_id
 * @property int                            $account_gateway_id
 * @property int                            $client_id
 * @property string                         $token
 * @property Carbon|null                    $created_at
 * @property Carbon|null                    $updated_at
 * @property Carbon|null                    $deleted_at
 * @property int|null                       $default_payment_method_id
 * @property AccountGateway                 $account_gateway
 * @property Contact                        $contact
 * @property PaymentMethod|null             $default_payment_method
 * @property Collection<int, PaymentMethod> $payment_methods
 * @property int|null                       $payment_methods_count
 *
 * @method static Builder|AccountGatewayToken clientAndGateway($clientId, $accountGatewayId)
 * @method static Builder|AccountGatewayToken newModelQuery()
 * @method static Builder|AccountGatewayToken newQuery()
 * @method static Builder|AccountGatewayToken onlyTrashed()
 * @method static Builder|AccountGatewayToken query()
 * @method static Builder|AccountGatewayToken whereAccountGatewayId($value)
 * @method static Builder|AccountGatewayToken whereAccountId($value)
 * @method static Builder|AccountGatewayToken whereClientId($value)
 * @method static Builder|AccountGatewayToken whereContactId($value)
 * @method static Builder|AccountGatewayToken whereCreatedAt($value)
 * @method static Builder|AccountGatewayToken whereDefaultPaymentMethodId($value)
 * @method static Builder|AccountGatewayToken whereDeletedAt($value)
 * @method static Builder|AccountGatewayToken whereId($value)
 * @method static Builder|AccountGatewayToken whereToken($value)
 * @method static Builder|AccountGatewayToken whereUpdatedAt($value)
 * @method static Builder|AccountGatewayToken withTrashed()
 * @method static Builder|AccountGatewayToken withoutTrashed()
 *
 * @mixin \Eloquent
 */
class AccountGatewayToken extends Model
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
        return $this->hasMany(PaymentMethod::class);
    }

    public function account_gateway()
    {
        return $this->belongsTo(AccountGateway::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * @return HasOne
     */
    public function default_payment_method()
    {
        return $this->hasOne(PaymentMethod::class, 'id', 'default_payment_method_id');
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
