<?php

namespace App\Http\Requests;

class CreateProposalRequest extends ProposalRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_PROPOSAL);
    }

    public function rules(): array
    {
        return [
            'invoice_id' => 'required',
        ];
    }
}
