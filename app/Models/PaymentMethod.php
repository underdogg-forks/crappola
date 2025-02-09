<?php

namespace App\Models;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use stdClass;

/**
 * Class PaymentMethod.
 *
 * @property int                        $id
 * @property int                        $account_id
 * @property int                        $user_id
 * @property int|null                   $contact_id
 * @property int|null                   $account_gateway_token_id
 * @property int                        $payment_type_id
 * @property string                     $source_reference
 * @property int|null                   $routing_number
 * @property null|string                $last4
 * @property string|null                $expiration
 * @property string|null                $email
 * @property int|null                   $currency_id
 * @property string|null                $status
 * @property Carbon|null                $created_at
 * @property Carbon|null                $updated_at
 * @property Carbon|null                $deleted_at
 * @property int                        $public_id
 * @property null                       $bank_name
 * @property string|null                $ip
 * @property Account                    $account
 * @property AccountGatewayToken|null   $account_gateway_token
 * @property Contact|null               $contact
 * @property Currency|null              $currency
 * @property mixed|null|stdClass|string $bank_data
 * @property PaymentType                $payment_type
 *
 * @method static Builder|PaymentMethod clientId($clientId)
 * @method static Builder|PaymentMethod isBankAccount($isBank)
 * @method static Builder|PaymentMethod newModelQuery()
 * @method static Builder|PaymentMethod newQuery()
 * @method static Builder|PaymentMethod onlyTrashed()
 * @method static Builder|PaymentMethod query()
 * @method static Builder|PaymentMethod scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|PaymentMethod whereAccountGatewayTokenId($value)
 * @method static Builder|PaymentMethod whereAccountId($value)
 * @method static Builder|PaymentMethod whereBankName($value)
 * @method static Builder|PaymentMethod whereContactId($value)
 * @method static Builder|PaymentMethod whereCreatedAt($value)
 * @method static Builder|PaymentMethod whereCurrencyId($value)
 * @method static Builder|PaymentMethod whereDeletedAt($value)
 * @method static Builder|PaymentMethod whereEmail($value)
 * @method static Builder|PaymentMethod whereExpiration($value)
 * @method static Builder|PaymentMethod whereId($value)
 * @method static Builder|PaymentMethod whereIp($value)
 * @method static Builder|PaymentMethod whereLast4($value)
 * @method static Builder|PaymentMethod wherePaymentTypeId($value)
 * @method static Builder|PaymentMethod wherePublicId($value)
 * @method static Builder|PaymentMethod whereRoutingNumber($value)
 * @method static Builder|PaymentMethod whereSourceReference($value)
 * @method static Builder|PaymentMethod whereStatus($value)
 * @method static Builder|PaymentMethod whereUpdatedAt($value)
 * @method static Builder|PaymentMethod whereUserId($value)
 * @method static Builder|PaymentMethod withActiveOrSelected($id = false)
 * @method static Builder|PaymentMethod withArchived()
 * @method static Builder|PaymentMethod withTrashed()
 * @method static Builder|PaymentMethod withoutTrashed()
 *
 * @mixin \Eloquent
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

    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @param $routingNumber
     *
     * @return mixed|null|stdClass|string
     */
    public static function lookupBankData(string $routingNumber)
    {
        $cached = Cache::get('bankData:' . $routingNumber);

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
            Cache::put('bankData:' . $routingNumber, $data, 5 * 60);

            return $data;
        }

        Cache::put('bankData:' . $routingNumber, false, 5 * 60);
        return null;
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function account_gateway_token()
    {
        return $this->belongsTo(AccountGatewayToken::class);
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

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
     */
    public function getLast4Attribute($value): ?string
    {
        return $value ? mb_str_pad($value, 4, '0', STR_PAD_LEFT) : null;
    }

    /**
     * @param $query
     * @param $clientId
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
     * @return UrlGenerator|string
     */
    public function imageUrl()
    {
        return url(sprintf('/images/credit_cards/%s.png', str_replace(' ', '', mb_strtolower($this->payment_type->name))));
    }

    public function requiresDelayedAutoBill(): bool
    {
        return $this->payment_type_id == PAYMENT_TYPE_ACH;
    }

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
