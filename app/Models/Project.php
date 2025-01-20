<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class ExpenseCategory.
 */
class Project extends EntityModel
{
    // Expense Categories
    use SoftDeletes;
    use PresentableTrait;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'task_rate',
        'private_notes',
        'due_at',
        'budgeted_hours',
        'custom_value1',
        'custom_value2',
    ];

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ProjectPresenter';

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PROJECT;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/projects/{$this->public_id}";
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    /**
     * @return HasMany
     */
    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($query) use ($startDate, $endDate): void {
            $query->whereBetween('due_at', [$startDate, $endDate]);
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
