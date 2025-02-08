<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class PaymentTerm.
 *
 * @property int                   $id
 * @property int                   $user_id
 * @property int                   $account_id
 * @property Carbon|null           $created_at
 * @property Carbon|null           $updated_at
 * @property Carbon|null           $deleted_at
 * @property string|null           $name
 * @property int                   $sort_order
 * @property int                   $public_id
 * @property Collection<int, Task> $tasks
 * @property int|null              $tasks_count
 *
 * @method static Builder|TaskStatus newModelQuery()
 * @method static Builder|TaskStatus newQuery()
 * @method static Builder|TaskStatus onlyTrashed()
 * @method static Builder|TaskStatus query()
 * @method static Builder|TaskStatus scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|TaskStatus whereAccountId($value)
 * @method static Builder|TaskStatus whereCreatedAt($value)
 * @method static Builder|TaskStatus whereDeletedAt($value)
 * @method static Builder|TaskStatus whereId($value)
 * @method static Builder|TaskStatus whereName($value)
 * @method static Builder|TaskStatus wherePublicId($value)
 * @method static Builder|TaskStatus whereSortOrder($value)
 * @method static Builder|TaskStatus whereUpdatedAt($value)
 * @method static Builder|TaskStatus whereUserId($value)
 * @method static Builder|TaskStatus withActiveOrSelected($id = false)
 * @method static Builder|TaskStatus withArchived()
 * @method static Builder|TaskStatus withTrashed()
 * @method static Builder|TaskStatus withoutTrashed()
 *
 * @mixin \Eloquent
 */
class TaskStatus extends EntityModel
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
        'name',
        'sort_order',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_TASK_STATUS;
    }

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('task_status_sort_order');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
