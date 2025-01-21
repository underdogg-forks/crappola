<?php

namespace App\Http\Requests;

class ClientRequest extends EntityRequest
{
    public $entityType = ENTITY_CLIENT;

    public function entity()
    {
        $client = parent::entity();

        // eager load the contacts
        if ($client && ! $client->relationLoaded('contacts')) {
            $client->load('contacts');
        }

        return $client;
    }
}
