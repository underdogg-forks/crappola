<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AccountGatewayToken.
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
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $casts = [];

    /**
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'account_gateway_id',
        'client_id',
        'token',
    ];

    /**
     * @return HasMany
     */
    public function payment_methods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    /**
     * @return BelongsTo
     */
    public function account_gateway()
    {
        return $this->belongsTo(AccountGateway::class);
    }

    /**
     * @return BelongsTo
     */
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

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_CUSTOMER;
    }

    /**
     * @return mixed
     */
    public function autoBillLater()
    {
        if ($this->default_payment_method) {
            return $this->default_payment_method->requiresDelayedAutoBill();
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function scopeClientAndGateway($query, $clientId, $companyGatewayId)
    {
        $query->where('client_id', '=', $clientId)
            ->where('account_gateway_id', '=', $companyGatewayId);

        return $query;
    }

    /**
     * @return mixed
     */
    public function gatewayName()
    {
        return $this->account_gateway->gateway->name;
    }

    /**
     * @return bool|string
     */
    public function gatewayLink()
    {
        $companyGateway = $this->account_gateway;
        if ($companyGateway->gateway_id == GATEWAY_STRIPE) {
            return "https://dashboard.stripe.com/customers/{$this->token}";
        }

        if ($companyGateway->gateway_id == GATEWAY_BRAINTREE) {
            $merchantId = $companyGateway->getConfigField('merchantId');
            $testMode = $companyGateway->getConfigField('testMode');

            return $testMode ? "https://sandbox.braintreegateway.com/merchants/{$merchantId}/customers/{$this->token}" : "https://www.braintreegateway.com/merchants/{$merchantId}/customers/{$this->token}";
        } elseif ($companyGateway->gateway_id == GATEWAY_GOCARDLESS) {
            $testMode = $companyGateway->getConfigField('testMode');

            return $testMode ? "https://manage-sandbox.gocardless.com/customers/{$this->token}" : "https://manage.gocardless.com/customers/{$this->token}";
        }

        return false;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
