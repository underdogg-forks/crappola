<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class TaskPolicy.
 */
class TaskPolicy extends EntityPolicy
{
    /**
     * @return bool
     */
    public function create(User $user)
    {
        if (! $this->createPermission($user, ENTITY_TASK)) {
            return false;
        }

        return $user->hasFeature(FEATURE_TASKS);
    }
}
