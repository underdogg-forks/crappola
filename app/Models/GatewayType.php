<?php

namespace App\Models;

use Cache;
use Eloquent;
use Utils;

/**
 * Class GatewayType.
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

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
