<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Subscription.
 */
class Subscription extends EntityModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'event_id',
        'target_url',
        'format',
    ];

    public function getEntityType()
    {
        return ENTITY_SUBSCRIPTION;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
