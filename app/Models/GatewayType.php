<?php

namespace App\Models;

use App\Libraries\Utils;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Cache;

/**
 * Class GatewayType.
 */
class GatewayType extends Eloquent
{
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
