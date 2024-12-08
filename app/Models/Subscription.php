<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Subscription.
 */
class Subscription extends EntityModel
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $fillable = [
        'event_id',
        'target_url',
        'format',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_SUBSCRIPTION;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }
}
