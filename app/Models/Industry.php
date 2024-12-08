<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Industry.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static Builder|Industry newModelQuery()
 * @method static Builder|Industry newQuery()
 * @method static Builder|Industry query()
 * @method static Builder|Industry whereId($value)
 * @method static Builder|Industry whereName($value)
 *
 * @mixin \Eloquent
 */
class Industry extends Model
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
