<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class SubscriptionPolicy.
 */
class SubscriptionPolicy extends EntityPolicy
{
    /**
     * @return bool
     */
    public function edit(User $user, $item)
    {
        return $user->hasPermission('admin');
    }

    /**
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('admin');
    }
}
