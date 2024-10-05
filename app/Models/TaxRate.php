<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TaxRate.
 */
class TaxRate extends EntityModel
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'rate',
        'is_inclusive',
    ];
    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @return bool|string
     */
    public function __toString(): string
    {
        return sprintf('%s: %s%%', $this->name, $this->rate);
    }

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_TAX_RATE;
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }
}
