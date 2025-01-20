<?php

namespace App\Http\Requests;

class TicketMergeRequest extends EntityRequest
{
    protected $entityType = ENTITY_TICKET;

    public function entity()
    {
        return parent::entity();
    }

    /**
     * @return array{updated_ticket_id: string}
     */
    public function rules(): array
    {
        return [
            'updated_ticket_id' => 'required',
        ];
    }
}
