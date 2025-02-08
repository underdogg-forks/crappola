<?php

namespace App\Models;

use DateTimeInterface;
use Utils;

/**
 * Class AccountGatewaySettings.
 */
class AccountGatewaySettings extends EntityModel
{
    /**
     * @var bool
     */
    protected static $hasPublicId = false;

    /**
     * @var array
     */
    protected $dates = ['updated_at'];

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
        'fee_cap',
        'adjust_fee_percent',
    ];

    /**
     * @return BelongsTo
     */
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
        return floatval($this->fee_amount) || floatval($this->fee_percent);
    }

    public function hasTaxes(): bool
    {
        return floatval($this->fee_tax_rate1) || floatval($this->fee_tax_rate2);
    }

    public function feesToString(): string
    {
        $parts = [];

        if (floatval($this->fee_amount) != 0) {
            $parts[] = Utils::formatMoney($this->fee_amount);
        }

        if (floatval($this->fee_percent) != 0) {
            $parts[] = (floor($this->fee_percent * 1000) / 1000) . '%';
        }

        return implode(' + ', $parts);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
