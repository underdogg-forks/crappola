<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class TicketCategoryPolicy.
 */
class TicketCategoryPolicy extends EntityPolicy
{
    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function create(User $user)
    {
        if (! $this->createPermission($user, ENTITY_TICKET_CATEGORY)) {
            return false;
        }

        return true;
    }
}
