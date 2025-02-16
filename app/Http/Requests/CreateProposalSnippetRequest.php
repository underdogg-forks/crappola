<?php

namespace App\Http\Requests;

class CreateProposalSnippetRequest extends ProposalSnippetRequest
{
    public function authorize()
    {
        return $this->user()->can('create', ENTITY_PROPOSAL_SNIPPET);
    }

    public function rules()
    {
        return [
            'name' => sprintf('required|unique:proposal_snippets,name,,id,account_id,%s', $this->user()->account_id),
        ];
    }
}
