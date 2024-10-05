<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

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
    public function getEntityType(): string
    {
        return ENTITY_TASK_STATUS;
    }

    /**
     * @return mixed
     */
    public function tasks()
    {
        return $this->hasMany(\App\Models\Task::class)->orderBy('task_status_sort_order');
    }
}
