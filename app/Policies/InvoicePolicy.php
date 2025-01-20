<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class InvoicePolicy.
 */
class InvoicePolicy extends EntityPolicy
{
    /**
     * @return bool
     */
    public function create(User $user)
    {
        return $this->createPermission($user, ENTITY_INVOICE);
    }

    /**
     * @return bool
     */
    public function view(User $user, $item, $entityType = null)
    {
        $entityType = is_string($item) ? $item : $item->getEntityType();

        return $user->hasPermission('view_' . $entityType) || $user->owns($item);
    }
}
