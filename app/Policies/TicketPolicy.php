<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class TicketPolicy.
 */
class TicketPolicy extends EntityPolicy
{
    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function create(User $user)
    {
        if (! $this->createPermission($user, ENTITY_TICKET)) {
            return false;
        }

        return $user->hasFeature(FEATURE_TICKETS);
    }

    /**
     * @return bool
     */
    public function view(User $user, $item, $entityType = null)
    {
        if (! $entityType) {
            $entityType = is_string($item) ? $item : $item->getEntityType();
        }

        return $user->hasPermission('view_' . $entityType) || $user->owns($item) || $user->id == $item->agent_id;
    }

    /**
     * @return bool
     */
    public function edit(User $user, $item, $entityType = null)
    {
        if (! $entityType) {
            $entityType = is_string($item) ? $item : $item->getEntityType();
        }

        return $user->hasPermission('edit_' . $entityType) || $user->owns($item) || $user->id == $item->agent_id;
    }

    /**
     * @return bool
     */
    public function isMergeable(User $user, $item)
    {
        if ($item->is_internal == false && $item->status_id != TICKET_STATUS_MERGED) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isTicketMaster(User $user, $item)
    {
        return $user->isTicketMaster() || $user->is_admin;
    }
}
