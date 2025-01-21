<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Theme.
 *
 * @property int    $id
 * @property string $name
 *
 * @method static Builder|Theme newModelQuery()
 * @method static Builder|Theme newQuery()
 * @method static Builder|Theme query()
 * @method static Builder|Theme whereId($value)
 * @method static Builder|Theme whereName($value)
 *
 * @mixin \Eloquent
 */
class Theme extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
}
