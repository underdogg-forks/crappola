<?php

namespace App\Models;

use App\Libraries\Utils;
use Cache;
use Eloquent;

/**
 * Class GatewayType.
 */
class GatewayType extends Eloquent
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

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
