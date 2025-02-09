<?php

namespace App\Http\Requests;

class UpdateProposalCategoryRequest extends ProposalCategoryRequest
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
            'name' => sprintf('required|unique:proposal_categories,name,,id,account_id,%s', $this->user()->account_id),
        ];
    }
}
