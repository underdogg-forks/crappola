<?php

namespace App\Http\Requests;

class UpdateProposalSnippetRequest extends ProposalSnippetRequest
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
            'name' => sprintf('required|unique:proposal_snippets,name,%s,id,account_id,%s', $this->entity()->id, $this->user()->account_id),
        ];
    }
}
