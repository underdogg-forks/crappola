<?php

namespace App\Policies;

use App\Models\User;

/**
 * Class ProposalTemplatePolicy.
 */
class ProposalTemplatePolicy extends EntityPolicy
{
    /**
     * @return bool
     */
    public function create(User $user)
    {
        return $this->createPermission($user, ENTITY_PROPOSAL);
    }
}
