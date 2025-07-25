<?php

namespace App\Models;

use App\Libraries\Utils;
use DateTimeInterface;

/**
 * Class AccountGatewaySettings.
 */
class AccountGatewaySettings extends EntityModel
{
    protected $dates = ['updated_at'];

    protected $fillable = [
        'fee_amount',
        'fee_percent',
        'fee_tax_name1',
        'fee_tax_rate1',
        'fee_tax_name2',
        'fee_tax_rate2',
    ];

    protected static $hasPublicId = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gatewayType()
    {
        return $this->belongsTo('App\Models\GatewayType');
    }

    public function setCreatedAtAttribute($value)
    {
        // to Disable created_at
    }

    public function areFeesEnabled()
    {
        return (float) ($this->fee_amount) || (float) ($this->fee_percent);
    }

    public function hasTaxes()
    {
        return (float) ($this->fee_tax_rate1) || (float) ($this->fee_tax_rate2);
    }

    public function feesToString()
    {
        $parts = [];

        if ((float) ($this->fee_amount) != 0) {
            $parts[] = Utils::formatMoney($this->fee_amount);
        }

        if ((float) ($this->fee_percent) != 0) {
            $parts[] = (floor($this->fee_percent * 1000) / 1000) . '%';
        }

        return join(' + ', $parts);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
