<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use stdClass;

/**
 * Class PaymentMethod.
 */
class PaymentMethod extends EntityModel
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
    protected $hidden = ['id'];

    /**
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'payment_type_id',
        'source_reference',
        'last4',
        'expiration',
        'email',
        'currency_id',
    ];

    /**
     * @param $routingNumber
     *
     * @return mixed|null|stdClass|string
     */
    public static function lookupBankData(string $routingNumber)
    {
        $cached = \Illuminate\Support\Facades\Cache::get('bankData:' . $routingNumber);

        if ($cached != null) {
            return $cached == false ? null : $cached;
        }

        $dataPath = base_path('app/Ninja/PaymentDrivers/FedACHdir.txt');

        if ( ! file_exists($dataPath) || ! $size = filesize($dataPath)) {
            return 'Invalid data file';
        }

        $lineSize = 157;
        $numLines = $size / $lineSize;

        if ($numLines % 1 !== 0) {
            // The number of lines should be an integer
            return 'Invalid data file';
        }

        // Format: http://www.sco.ca.gov/Files-21C/Bank_Master_Interface_Information_Package.pdf
        $file = fopen($dataPath, 'r');

        // Binary search
        $low = 0;
        $high = $numLines - 1;
        while ($low <= $high) {
            $mid = floor(($low + $high) / 2);

            fseek($file, $mid * $lineSize);
            $thisNumber = fread($file, 9);

            if ($thisNumber > $routingNumber) {
                $high = $mid - 1;
            } elseif ($thisNumber < $routingNumber) {
                $low = $mid + 1;
            } else {
                $data = new stdClass();
                $data->routing_number = $thisNumber;

                fseek($file, 26, SEEK_CUR);

                $data->name = trim(fread($file, 36));
                $data->address = trim(fread($file, 36));
                $data->city = trim(fread($file, 20));
                $data->state = fread($file, 2);
                $data->zip = fread($file, 5) . '-' . fread($file, 4);
                $data->phone = fread($file, 10);
                break;
            }
        }

        if ( ! empty($data)) {
            \Illuminate\Support\Facades\Cache::put('bankData:' . $routingNumber, $data, 5 * 60);

            return $data;
        }
        \Illuminate\Support\Facades\Cache::put('bankData:' . $routingNumber, false, 5 * 60);
        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(\App\Models\Contact::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account_gateway_token()
    {
        return $this->belongsTo(\App\Models\AccountGatewayToken::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_type()
    {
        return $this->belongsTo(\App\Models\PaymentType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany('App\Models\Payments');
    }

    /**
     * @return mixed|null|stdClass|string
     */
    public function getBankDataAttribute()
    {
        if ( ! $this->routing_number) {
            return null;
        }

        return static::lookupBankData($this->routing_number);
    }

    /**
     * @param $bank_name
     *
     * @return null
     */
    public function getBankNameAttribute($bank_name)
    {
        if ($bank_name) {
            return $bank_name;
        }
        $bankData = $this->bank_data;

        return $bankData ? $bankData->name : null;
    }

    /**
     * @param $value
     *
     * @return null|string
     */
    public function getLast4Attribute($value): ?string
    {
        return $value ? str_pad($value, 4, '0', STR_PAD_LEFT) : null;
    }

    /**
     * @param $query
     * @param $clientId
     *
     * @return mixed
     */
    public function scopeClientId($query, $clientId): void
    {
        $query->whereHas('contact', function ($query) use ($clientId): void {
            $query->withTrashed()->whereClientId($clientId);
        });
    }

    /**
     * @param $query
     * @param $isBank
     */
    public function scopeIsBankAccount($query, $isBank): void
    {
        if ($isBank) {
            $query->where('payment_type_id', '=', PAYMENT_TYPE_ACH);
        } else {
            $query->where('payment_type_id', '!=', PAYMENT_TYPE_ACH);
        }
    }

    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function imageUrl()
    {
        return url(sprintf('/images/credit_cards/%s.png', str_replace(' ', '', mb_strtolower($this->payment_type->name))));
    }

    /**
     * @return bool
     */
    public function requiresDelayedAutoBill(): bool
    {
        return $this->payment_type_id == PAYMENT_TYPE_ACH;
    }

    /**
     * @return mixed
     */
    public function gatewayType(): int|string
    {
        if ($this->payment_type_id == PAYMENT_TYPE_ACH) {
            return GATEWAY_TYPE_BANK_TRANSFER;
        }
        if ($this->payment_type_id == PAYMENT_TYPE_PAYPAL) {
            return GATEWAY_TYPE_PAYPAL;
        }

        return GATEWAY_TYPE_TOKEN;
    }
}

PaymentMethod::deleting(function ($paymentMethod): void {
    $accountGatewayToken = $paymentMethod->account_gateway_token;
    if ($accountGatewayToken && $accountGatewayToken->default_payment_method_id == $paymentMethod->id) {
        $newDefault = $accountGatewayToken->payment_methods->first(fn ($paymentMethdod): bool => $paymentMethdod->id != $accountGatewayToken->default_payment_method_id);
        $accountGatewayToken->default_payment_method_id = $newDefault ? $newDefault->id : null;
        $accountGatewayToken->save();
    }
});
