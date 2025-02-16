<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class ExpenseCategory.
 */
class DbServer extends Eloquent
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
