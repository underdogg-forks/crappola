<?php

namespace App\Models;

use App\Libraries\Utils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class GatewayType.
 *
 * @property int    $id
 * @property string $alias
 * @property string $name
 *
 * @method static Builder|GatewayType newModelQuery()
 * @method static Builder|GatewayType newQuery()
 * @method static Builder|GatewayType query()
 * @method static Builder|GatewayType whereAlias($value)
 * @method static Builder|GatewayType whereId($value)
 * @method static Builder|GatewayType whereName($value)
 *
 * @mixin \Eloquent
 */
class GatewayType extends Model
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
        return Cache::get('gatewayTypes')->where('alias', $alias)->first()->id;
    }

    public function getName()
    {
        return $this->name;
    }
}
