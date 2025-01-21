<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class PaymentTermPolicy.
 */
class PaymentTermPolicy extends EntityPolicy
{
    /**
     * @return mixed
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
