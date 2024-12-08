<?php

namespace App\Models;

/**
 * Class Industry.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Industry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Industry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Industry query()
 * @method static \Illuminate\Database\Eloquent\Builder|Industry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Industry whereName($value)
 *
 * @mixin \Eloquent
 */
class Industry extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public function getName()
    {
        return $this->name;
    }
}
