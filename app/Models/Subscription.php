<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Subscription.
 */
class Subscription extends EntityModel
{
    /**
     * @var bool
     */
    public $timestamps = true;

    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'event_id',
        'target_url',
        'format',
    ];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_SUBSCRIPTION;
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
