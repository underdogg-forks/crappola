<?php

namespace App\Http\Requests;

class CreateProposalRequest extends ProposalRequest
{
    public function authorize()
    {
        return $this->user()->can('create', ENTITY_PROPOSAL);
    }

    public function rules()
    {
        return [
            'invoice_id' => 'required',
        ];
    }
}
