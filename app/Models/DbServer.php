<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ExpenseCategory.
 */
class DbServer extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
