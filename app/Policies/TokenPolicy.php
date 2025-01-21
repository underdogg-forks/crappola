<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class TokenPolicy.
 */
class TokenPolicy extends EntityPolicy
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
