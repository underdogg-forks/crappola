<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class EntityPolicy.
 */
class EntityPolicy
{
    use HandlesAuthorization;

    /**
     * @param $item - entity name or object
     *
     * @return bool
     */
    public static function create(User $user, $item)
    {
        if ( ! static::checkModuleEnabled($user, $item)) {
            return false;
        }

        $entityType = is_string($item) ? $item : $item->getEntityType();

        return $user->hasPermission('create_' . $entityType);
    }

    /**
     * @param $item - entity name or object
     *
     * @return bool
     */
    public static function edit(User $user, $item)
    {
        if ( ! static::checkModuleEnabled($user, $item)) {
            return false;
        }

        $entityType = is_string($item) ? $item : $item->getEntityType();
        if ($user->hasPermission('edit_' . $entityType)) {
            return true;
        }

        return $user->owns($item);
    }

    /**
     * @param $item - entity name or object
     *
     * @return bool
     */
    public static function view(User $user, $item)
    {
        if ( ! static::checkModuleEnabled($user, $item)) {
            return false;
        }

        $entityType = is_string($item) ? $item : $item->getEntityType();
        if ($user->hasPermission('view_' . $entityType)) {
            return true;
        }

        return $user->owns($item);
    }

    /**
     * @param $ownerUserId
     *
     * Legacy permissions - retaining these for legacy code however new code
     *                      should use auth()->user()->can('view', $ENTITY_TYPE)
     *
     * $ENTITY_TYPE can be either the constant ie ENTITY_INVOICE, or the entity $object
     */
    public static function viewByOwner(User $user, $ownerUserId): bool
    {
        return $user->id == $ownerUserId;
    }

    /**
     * @param $ownerUserId
     *
     * Legacy permissions - retaining these for legacy code however new code
     *                      should use auth()->user()->can('edit', $ENTITY_TYPE)
     *
     * $ENTITY_TYPE can be either the constant ie ENTITY_INVOICE, or the entity $object
     *
     * @return bool
     */
    public static function editByOwner(User $user, $ownerUserId): mixed
    {
        return $user->id == $ownerUserId;
    }

    /**
     * @param $item - entity name or object
     *
     * @return bool
     */
    private static function checkModuleEnabled(User $user, $item)
    {
        $entityType = is_string($item) ? $item : $item->getEntityType();

        return $user->account->isModuleEnabled($entityType);
    }
}
