<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 *
 * @property int                   $id
 * @property int                   $user_id
 * @property int                   $account_id
 * @property int|null              $client_id
 * @property Carbon|null           $created_at
 * @property Carbon|null           $updated_at
 * @property Carbon|null           $deleted_at
 * @property string|null           $name
 * @property int                   $is_deleted
 * @property int                   $public_id
 * @property string                $task_rate
 * @property string|null           $due_date
 * @property string|null           $private_notes
 * @property float                 $budgeted_hours
 * @property string|null           $custom_value1
 * @property string|null           $custom_value2
 * @property Account               $account
 * @property Client|null           $client
 * @property Collection<int, Task> $tasks
 * @property int|null              $tasks_count
 *
 * @method static Builder|Project dateRange($startDate, $endDate)
 * @method static Builder|Project newModelQuery()
 * @method static Builder|Project newQuery()
 * @method static Builder|Project onlyTrashed()
 * @method static Builder|Project query()
 * @method static Builder|Project scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|Project whereAccountId($value)
 * @method static Builder|Project whereBudgetedHours($value)
 * @method static Builder|Project whereClientId($value)
 * @method static Builder|Project whereCreatedAt($value)
 * @method static Builder|Project whereCustomValue1($value)
 * @method static Builder|Project whereCustomValue2($value)
 * @method static Builder|Project whereDeletedAt($value)
 * @method static Builder|Project whereDueDate($value)
 * @method static Builder|Project whereId($value)
 * @method static Builder|Project whereIsDeleted($value)
 * @method static Builder|Project whereName($value)
 * @method static Builder|Project wherePrivateNotes($value)
 * @method static Builder|Project wherePublicId($value)
 * @method static Builder|Project whereTaskRate($value)
 * @method static Builder|Project whereUpdatedAt($value)
 * @method static Builder|Project whereUserId($value)
 * @method static Builder|Project withActiveOrSelected($id = false)
 * @method static Builder|Project withArchived()
 * @method static Builder|Project withTrashed()
 * @method static Builder|Project withoutTrashed()
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
    protected $presenter = ProjectPresenter::class;

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
        return $this->belongsTo(Account::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

Project::creating(function ($project): void {
    $project->setNullValues();
});

Project::updating(function ($project): void {
    $project->setNullValues();
});
