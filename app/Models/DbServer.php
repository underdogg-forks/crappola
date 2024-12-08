<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExpenseCategory.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static Builder|DbServer newModelQuery()
 * @method static Builder|DbServer newQuery()
 * @method static Builder|DbServer query()
 * @method static Builder|DbServer whereId($value)
 * @method static Builder|DbServer whereName($value)
 *
 * @mixin \Eloquent
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
