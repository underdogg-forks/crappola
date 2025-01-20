<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
}
