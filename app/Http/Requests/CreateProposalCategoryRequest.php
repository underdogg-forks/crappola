<?php

namespace App\Http\Requests;

use App\Models\Proposal;
use App\Models\ProposalCategory;

class CreateProposalCategoryRequest extends ProposalCategoryRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user()->can('create', Proposal::class)) {
            return true;
        }

        return (bool) $this->user()->can('create', ProposalCategory::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{name: string}
     */
    public function rules(): array
    {
        return [
            'name' => sprintf('required|unique:proposal_categories,name,,id,company_id,%s', $this->user()->company_id),
        ];
    }
}
