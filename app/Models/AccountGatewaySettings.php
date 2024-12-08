<?php

namespace App\Models;

use Utils;

/**
 * Class AccountGatewaySettings.
 *
 * @property int                             $id
 * @property int                             $account_id
 * @property int                             $user_id
 * @property int|null                        $gateway_type_id
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null                        $min_limit
 * @property int|null                        $max_limit
 * @property string|null                     $fee_amount
 * @property string|null                     $fee_percent
 * @property string|null                     $fee_tax_name1
 * @property string|null                     $fee_tax_name2
 * @property string|null                     $fee_tax_rate1
 * @property string|null                     $fee_tax_rate2
 * @property \App\Models\GatewayType|null    $gatewayType
 * @property mixed                           $created_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereFeeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereFeePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereFeeTaxName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereFeeTaxName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereFeeTaxRate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereFeeTaxRate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereGatewayTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereMaxLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereMinLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|AccountGatewaySettings withArchived()
 *
 * @mixin \Eloquent
 */
class AccountGatewaySettings extends EntityModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'fee_amount',
        'fee_percent',
        'fee_tax_name1',
        'fee_tax_rate1',
        'fee_tax_name2',
        'fee_tax_rate2',
    ];

    /**
     * @var bool
     */
    protected static $hasPublicId = false;

    protected $casts = ['updated_at' => 'datetime'];

    public function gatewayType()
    {
        return $this->belongsTo(\App\Models\GatewayType::class);
    }

    public function setCreatedAtAttribute($value): void
    {
        // to Disable created_at
    }

    public function areFeesEnabled(): bool
    {
        return (float) ($this->fee_amount) || (float) ($this->fee_percent);
    }

    public function hasTaxes(): bool
    {
        return (float) ($this->fee_tax_rate1) || (float) ($this->fee_tax_rate2);
    }

    public function feesToString(): string
    {
        $parts = [];

        if ((float) ($this->fee_amount) != 0) {
            $parts[] = Utils::formatMoney($this->fee_amount);
        }

        if ((float) ($this->fee_percent) != 0) {
            $parts[] = (floor($this->fee_percent * 1000) / 1000) . '%';
        }

        return implode(' + ', $parts);
    }
}
