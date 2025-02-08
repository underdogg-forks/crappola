<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class License.
 */
class License extends Model
{
    /**
     * @var bool
     */
    public $timestamps = true;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
