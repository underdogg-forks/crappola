<?php

namespace App\Models;

/**
 * Class Theme.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Theme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Theme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Theme query()
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Theme whereName($value)
 *
 * @mixin \Eloquent
 */
class Theme extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}
