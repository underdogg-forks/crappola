<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class ProjectPolicy.
 */
class ProjectPolicy extends EntityPolicy
{
    /**
     * @return bool
     */
    public function create(User $user)
    {
        return $this->createPermission($user, ENTITY_PROJECT);
    }
}
