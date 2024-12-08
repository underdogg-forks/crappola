<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int                                                             $id
 * @property int                                                             $user_id
 * @property int                                                             $account_id
 * @property int|null                                                        $client_id
 * @property \Illuminate\Support\Carbon|null                                 $created_at
 * @property \Illuminate\Support\Carbon|null                                 $updated_at
 * @property \Illuminate\Support\Carbon|null                                 $deleted_at
 * @property string|null                                                     $name
 * @property int                                                             $is_deleted
 * @property int                                                             $public_id
 * @property string                                                          $task_rate
 * @property string|null                                                     $due_date
 * @property string|null                                                     $private_notes
 * @property float                                                           $budgeted_hours
 * @property string|null                                                     $custom_value1
 * @property string|null                                                     $custom_value2
 * @property \App\Models\Account                                             $account
 * @property \App\Models\Client|null                                         $client
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property int|null                                                        $tasks_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Project dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|Project scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereBudgetedHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereTaskRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Project withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Project withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Project withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Project extends EntityModel
{
    use PresentableTrait;
    // Expense Categories
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'task_rate',
        'private_notes',
        'due_date',
        'budgeted_hours',
        'custom_value1',
        'custom_value2',
    ];

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\ProjectPresenter::class;

    protected $casts = ['deleted_at' => 'datetime'];

    public function getEntityType(): string
    {
        return ENTITY_PROJECT;
    }

    public function getRoute(): string
    {
        return '/projects/' . $this->public_id;
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class)->withTrashed();
    }

    public function tasks()
    {
        return $this->hasMany(\App\Models\Task::class);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($query) use ($startDate, $endDate): void {
            $query->whereBetween('due_date', [$startDate, $endDate]);
        });
    }

    public function getDisplayName()
    {
        return $this->name;
    }
}

Project::creating(function ($project): void {
    $project->setNullValues();
});

Project::updating(function ($project): void {
    $project->setNullValues();
});
