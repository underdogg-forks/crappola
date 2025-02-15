<?php

namespace App\Models;

use DateTimeInterface;
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

    protected $dates = ['deleted_at'];

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
    protected $presenter = 'App\Ninja\Presenters\ProjectPresenter';

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($query) use ($startDate, $endDate) {
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

Project::creating(function ($project) {
    $project->setNullValues();
});

Project::updating(function ($project) {
    $project->setNullValues();
});
