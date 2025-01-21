<?php

namespace App\Http\Requests;

class UpdateProposalTemplateRequest extends ProposalTemplateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! $this->entity()) {
            return false;
        }

        return (bool) $this->user()->can('edit', $this->entity());
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if (! $this->entity()) {
            return [];
        }

        return [
            'name' => sprintf('required|unique:proposal_templates,name,%s,id,company_id,%s', $this->entity()->id, $this->user()->company_id),
        ];
    }
}
