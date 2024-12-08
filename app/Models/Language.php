<?php

namespace App\Models;

/**
 * Class Language.
 *
 * @property int    $id
 * @property string $name
 * @property string $locale
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereName($value)
 *
 * @mixin \Eloquent
 */
class Language extends \Illuminate\Database\Eloquent\Model
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
