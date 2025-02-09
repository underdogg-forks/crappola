<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class PaymentTermPolicy.
 */
class PaymentTermPolicy extends EntityPolicy
{
    /**
     * @param      $item
     * @return mixed
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
