<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Subscription.
 *
 * @property int                             $id
 * @property int|null                        $account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null                        $event_id
 * @property string                          $target_url
 * @property int|null                        $public_id
 * @property int|null                        $user_id
 * @property string                          $format
 * @property \App\Models\Account|null        $account
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereTargetUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription withoutTrashed()
 *
 * @mixin \Eloquent
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

    public function getEntityType(): string
    {
        return ENTITY_SUBSCRIPTION;
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }
}
