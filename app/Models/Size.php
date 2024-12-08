<?php

namespace App\Models;

/**
 * Class Size.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Size newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Size newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Size query()
 * @method static \Illuminate\Database\Eloquent\Builder|Size whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Size whereName($value)
 *
 * @mixin \Eloquent
 */
class Size extends \Illuminate\Database\Eloquent\Model
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
