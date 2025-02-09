<?php

namespace App\Policies;

use App\Models\User;

class ExpenseCategoryPolicy extends EntityPolicy
{
    /**
     * @param mixed $item
     * @return bool
     */
    public static function create(User $user, $item)
    {
        return $user->is_admin;
    }

    /**
     * @param      $item
     * @return bool
     */
    public static function edit(User $user, $item)
    {
        return $user->is_admin;
    }

    /**
     * @param      $item
     *
     */
    public static function view(User $user, $item): bool
    {
        return true;
    }

    /**
     * @param      $ownerUserId
     *
     */
    public static function viewByOwner(User $user, $ownerUserId): bool
    {
        return true;
    }

    /**
     * @param      $ownerUserId
     *
     */
    public static function editByOwner(User $user, $ownerUserId): bool
    {
        return $user->is_admin;
    }
}
