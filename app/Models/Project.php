<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
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

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_PROJECT;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return '/projects/' . $this->public_id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
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
