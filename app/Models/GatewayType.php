<?php

namespace App\Models;

use Utils;

/**
 * Class GatewayType.
 *
 * @property int    $id
 * @property string $alias
 * @property string $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GatewayType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GatewayType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GatewayType query()
 * @method static \Illuminate\Database\Eloquent\Builder|GatewayType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GatewayType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GatewayType whereName($value)
 *
 * @mixin \Eloquent
 */
class GatewayType extends \Illuminate\Database\Eloquent\Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    public static function getAliasFromId($id)
    {
        return Utils::getFromCache($id, 'gatewayTypes')->alias;
    }

    public static function getIdFromAlias($alias)
    {
        return \Illuminate\Support\Facades\Cache::get('gatewayTypes')->where('alias', $alias)->first()->id;
    }

    public function getName()
    {
        return $this->name;
    }
}
