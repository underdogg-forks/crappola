<?php

namespace App\Models;

use Utils;

/**
 * Class AccountGatewaySettings.
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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
