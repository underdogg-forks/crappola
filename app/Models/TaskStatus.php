<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentTerm.
 *
 * @property int                                                             $id
 * @property int                                                             $user_id
 * @property int                                                             $account_id
 * @property \Illuminate\Support\Carbon|null                                 $created_at
 * @property \Illuminate\Support\Carbon|null                                 $updated_at
 * @property \Illuminate\Support\Carbon|null                                 $deleted_at
 * @property string|null                                                     $name
 * @property int                                                             $sort_order
 * @property int                                                             $public_id
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property int|null                                                        $tasks_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus withoutTrashed()
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
        return $this->hasMany(\App\Models\Task::class)->orderBy('task_status_sort_order');
    }
}
