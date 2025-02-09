<?php

namespace App\Policies;

use App\Models\User;

class TaxRatePolicy extends EntityPolicy
{
    /**
     * @param      $item
     * @return bool
     */
    public static function edit(User $user, $item)
    {
        return $user->hasPermission('admin');
    }

    /**
     * @param mixed $item
     * @return bool
     */
    public static function create(User $user, $item)
    {
        return $user->hasPermission('admin');
    }
}
