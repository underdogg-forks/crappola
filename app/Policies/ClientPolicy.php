<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class ClientPolicy.
 */
class ClientPolicy extends EntityPolicy
{
    /**
     * @return bool
     */
    public function create(User $user)
    {
        return $this->createPermission($user, ENTITY_CLIENT);
    }
}
