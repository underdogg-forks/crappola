<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class TaxRatePolicy.
 */
class TaxRatePolicy extends EntityPolicy
{
    /**
     * @return bool
     */
    public function edit(User $user, $item)
    {
        return $user->hasPermission('admin');
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('admin');
    }
}
