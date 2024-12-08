<?php

namespace App\Models;

/**
 * Class ExpenseCategory.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|DbServer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DbServer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DbServer query()
 * @method static \Illuminate\Database\Eloquent\Builder|DbServer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DbServer whereName($value)
 *
 * @mixin \Eloquent
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
