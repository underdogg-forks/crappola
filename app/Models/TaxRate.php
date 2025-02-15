<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TaxRate.
 */
class TaxRate extends EntityModel
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'rate',
        'is_inclusive',
    ];

    /**
     * @return bool|string
     */
    public function __toString()
    {
        return sprintf('%s: %s%%', $this->name, $this->rate);
    }

    public function getEntityType()
    {
        return ENTITY_TAX_RATE;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
