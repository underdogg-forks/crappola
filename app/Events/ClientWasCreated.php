<?php

namespace App\Events;

use App\Models\Client;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClientWasCreated.
 */
class ClientWasCreated extends Event
{
    use SerializesModels;

    /**
     * @var Client
     */
    public $client;

    /**
     * Create a new event instance.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
