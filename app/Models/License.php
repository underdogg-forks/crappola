<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class License.
 */
class License extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    /**
     * @var bool
     */
    public $timestamps = true;

    protected $casts = ['deleted_at' => 'datetime'];
}
