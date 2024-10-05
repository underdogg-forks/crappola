<?php

namespace App\Models;

use Eloquent;

/**
 * Class ExpenseCategory.
 */
class DbServer extends \Illuminate\Database\Eloquent\Model
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
