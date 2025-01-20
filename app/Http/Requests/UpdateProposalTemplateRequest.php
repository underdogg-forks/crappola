<?php

namespace App\Http\Requests;

class UpdateProposalTemplateRequest extends ProposalTemplateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (!$this->entity()) {
            return [];
        }

        return [
            'name' => sprintf('required|unique:proposal_templates,name,%s,id,company_id,%s', $this->entity()->id, $this->user()->company_id),
        ];
    }
}
