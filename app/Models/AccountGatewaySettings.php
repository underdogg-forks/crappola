<?php

namespace App\Models;

use App\Libraries\Utils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Class AccountGatewaySettings.
 *
 * @property int              $id
 * @property int              $account_id
 * @property int              $user_id
 * @property int|null         $gateway_type_id
 * @property Carbon|null      $updated_at
 * @property int|null         $min_limit
 * @property int|null         $max_limit
 * @property string|null      $fee_amount
 * @property string|null      $fee_percent
 * @property string|null      $fee_tax_name1
 * @property string|null      $fee_tax_name2
 * @property string|null      $fee_tax_rate1
 * @property string|null      $fee_tax_rate2
 * @property GatewayType|null $gatewayType
 * @property mixed            $created_at
 *
 * @method static Builder|AccountGatewaySettings newModelQuery()
 * @method static Builder|AccountGatewaySettings newQuery()
 * @method static Builder|AccountGatewaySettings query()
 * @method static Builder|AccountGatewaySettings scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|AccountGatewaySettings whereAccountId($value)
 * @method static Builder|AccountGatewaySettings whereFeeAmount($value)
 * @method static Builder|AccountGatewaySettings whereFeePercent($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxName1($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxName2($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxRate1($value)
 * @method static Builder|AccountGatewaySettings whereFeeTaxRate2($value)
 * @method static Builder|AccountGatewaySettings whereGatewayTypeId($value)
 * @method static Builder|AccountGatewaySettings whereId($value)
 * @method static Builder|AccountGatewaySettings whereMaxLimit($value)
 * @method static Builder|AccountGatewaySettings whereMinLimit($value)
 * @method static Builder|AccountGatewaySettings whereUpdatedAt($value)
 * @method static Builder|AccountGatewaySettings whereUserId($value)
 * @method static Builder|AccountGatewaySettings withActiveOrSelected($id = false)
 * @method static Builder|AccountGatewaySettings withArchived()
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
        return $this->belongsTo(GatewayType::class);
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
