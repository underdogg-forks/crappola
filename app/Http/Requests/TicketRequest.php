<?php

namespace App\Http\Requests;

class TicketRequest extends EntityRequest
{
    protected $entityType = ENTITY_TICKET;

    public function authorize(): bool
    {
        if (request()->is('tickets/create*') && $this->user()->can('createEntity', ENTITY_TICKET)) {
            return true;
        }
        if (! request()->is('tickets/*/edit')) {
            return false;
        }
        if (! $this->user()->can('view', $this->entity())) {
            return false;
        }

        return true;
    }

    public function entity()
    {
        $ticket = parent::entity();

        // eager load the documents
        if ($ticket && method_exists($ticket, 'documents') && ! $ticket->relationLoaded('documents')) {
            $ticket->load('documents', 'relations');
        }

        return $ticket;
    }
}
