<?php

namespace App\Http\Requests;

class CreateProposalTemplateRequest extends ProposalTemplateRequest
{
    public function authorize()
    {
        return $this->user()->can('create', ENTITY_PROPOSAL_TEMPLATE);
    }

    public function rules()
    {
        return [
            'name' => sprintf('required|unique:proposal_templates,name,,id,account_id,%s', $this->user()->account_id),
        ];
    }
}
