<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Subscription.
 *
 * @property int          $id
 * @property int|null     $account_id
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property Carbon|null  $deleted_at
 * @property int|null     $event_id
 * @property string       $target_url
 * @property int|null     $public_id
 * @property int|null     $user_id
 * @property string       $format
 * @property Account|null $account
 *
 * @method static Builder|Subscription newModelQuery()
 * @method static Builder|Subscription newQuery()
 * @method static Builder|Subscription onlyTrashed()
 * @method static Builder|Subscription query()
 * @method static Builder|Subscription scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|Subscription whereAccountId($value)
 * @method static Builder|Subscription whereCreatedAt($value)
 * @method static Builder|Subscription whereDeletedAt($value)
 * @method static Builder|Subscription whereEventId($value)
 * @method static Builder|Subscription whereFormat($value)
 * @method static Builder|Subscription whereId($value)
 * @method static Builder|Subscription wherePublicId($value)
 * @method static Builder|Subscription whereTargetUrl($value)
 * @method static Builder|Subscription whereUpdatedAt($value)
 * @method static Builder|Subscription whereUserId($value)
 * @method static Builder|Subscription withActiveOrSelected($id = false)
 * @method static Builder|Subscription withArchived()
 * @method static Builder|Subscription withTrashed()
 * @method static Builder|Subscription withoutTrashed()
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
        return $this->belongsTo(Account::class);
    }
}
