<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Language.
 *
 * @property int    $id
 * @property string $name
 * @property string $locale
 *
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language query()
 * @method static Builder|Language whereId($value)
 * @method static Builder|Language whereLocale($value)
 * @method static Builder|Language whereName($value)
 *
 * @mixin \Eloquent
 */
class Language extends Model
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
