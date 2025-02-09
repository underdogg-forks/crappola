<?php

namespace App\Policies;

use App\Models\User;

class VendorPolicy extends EntityPolicy
{
    /**
     * @param mixed $item
     * @return bool
     */
    public static function create(User $user, $item)
    {
        if ( ! parent::create($user, $item)) {
            return false;
        }

        return $user->hasFeature(FEATURE_EXPENSES);
    }
}
