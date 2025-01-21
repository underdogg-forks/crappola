<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class PaymentPolicy.
 */
class PaymentPolicy extends EntityPolicy
{
    /**
     * @return bool
     */
    public function create(User $user)
    {
        return $this->createPermission($user, ENTITY_PAYMENT);
    }
}
