<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class RecurringQuotePolicy.
 */
class RecurringQuotePolicy extends EntityPolicy
{
    /**
     * @return bool
     */
    public function create(User $user)
    {
        return $this->createPermission($user, ENTITY_QUOTE);
    }
}
