<?php

namespace App\Policies;

use App\Libraries\Utils;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

/**
 * Class GenericEntityPolicy.
 */
class GenericEntityPolicy
{
    use HandlesAuthorization;

    /**
     * @return bool|mixed
     */
    public static function editByOwner(User $user, $entityType, $ownerUserId)
    {
        $className = static::className($entityType);
        if (method_exists($className, 'editByOwner')) {
            return call_user_func([$className, 'editByOwner'], $user, $ownerUserId);
        }

        return false;
    }

    private static function className($entityType)
    {
        /* if (! Utils::isNinjaProd()) {
            if ($module = \Module::find($entityType)) {
                return "Modules\\{$module->getName()}\\Policies\\{$module->getName()}Policy";
            }
        } */

        $studly = Str::studly($entityType);

        return "App\\Policies\\{$studly}Policy";
    }

    /**
     * @param mixed $entityType
     *
     * @return bool|mixed
     */
    public static function viewByOwner(User $user, $entityType, $ownerUserId)
    {
        $className = static::className($entityType);
        if (method_exists($className, 'viewByOwner')) {
            return call_user_func([$className, 'viewByOwner'], $user, $ownerUserId);
        }

        return false;
    }

    /**
     * @return bool|mixed
     */
    public static function create(User $user, $entityType)
    {
        $className = static::className($entityType);
        if (method_exists($className, 'create')) {
            return call_user_func([$className, 'create'], $user, $entityType);
        }

        return false;
    }

    /**
     * @return bool|mixed
     */
    public static function view(User $user, $entityType)
    {
        $className = static::className($entityType);
        if (method_exists($className, 'view')) {
            return call_user_func([$className, 'view'], $user, $entityType);
        }

        return false;
    }

    /**
     * @param $item - entity name or object
     *
     * @return bool
     */
    public static function edit(User $user, $item)
    {
        if (! static::checkModuleEnabled($user, $item)) {
            return false;
        }

        $entityType = is_string($item) ? $item : $item->getEntityType();

        return $user->hasPermission('edit_' . $entityType) || $user->owns($item);
    }

    /**
     * @param $item - entity name or object
     *
     * @return bool
     */
    private static function checkModuleEnabled(User $user, $item)
    {
        $entityType = is_string($item) ? $item : $item->getEntityType();

        return $user->company->isModuleEnabled($entityType);
    }
}
