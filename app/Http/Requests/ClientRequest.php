<?php

namespace App\Http\Requests;


class ClientRequest extends Request
{
    protected string $entityType = ENTITY_CLIENT;

    /*public function entity()
    {
        $client = parent::entity();
        dd($client);

        // eager load the contacts
        if ($client && !$client->relationLoaded('contacts')) {
            $client->load('contacts');
        }

        return $client;
    }*/
    public function rules(): array
    {
        return [];
    }
}
