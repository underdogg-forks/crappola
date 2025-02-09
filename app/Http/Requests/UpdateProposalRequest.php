<?php

namespace App\Http\Requests;

class UpdateProposalRequest extends ProposalRequest
{

    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        if ( ! $this->entity()) {
            return [];
        }

        return [
            'invoice_id' => 'required',
        ];
    }
}
