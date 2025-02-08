<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * Class PaymentTerm.
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
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'sort_order',
    ];

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_TASK_STATUS;
    }

    /**
     * @return mixed
     */
    public function tasks(): Builder
    {
        return $this->hasMany(Task::class)->orderBy('task_status_sort_order');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
