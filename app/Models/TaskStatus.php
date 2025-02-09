<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentTerm.
 */
class TaskStatus extends EntityModel
{
    use SoftDeletes;

    public $timestamps = true;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'sort_order',
    ];

    public function getEntityType()
    {
        return ENTITY_TASK_STATUS;
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task')->orderBy('task_status_sort_order');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
