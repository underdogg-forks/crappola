<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class UserSettingsChanged extends Event
{
    use SerializesModels;

    /**
     * @var User|null
     */
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user = null)
    {
        $this->user = $user;
    }
}
