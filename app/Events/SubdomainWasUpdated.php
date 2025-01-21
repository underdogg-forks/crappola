<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class SubdomainWasUpdated extends Event
{
    use SerializesModels;

    public $company;

    /**
     * Create a new event instance.
     */
    public function __construct($company)
    {
        $this->company = $company;
    }
}
