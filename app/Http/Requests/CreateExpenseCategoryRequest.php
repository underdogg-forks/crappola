<?php

namespace App\Http\Requests;

use App\Models\Expense;

class CreateExpenseCategoryRequest extends ExpenseCategoryRequest
{
    // Expenses

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Expense::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{name: string}
     */
    public function rules(): array
    {
        return [
            'name' => sprintf('required|unique:expense_categories,name,,id,company_id,%s', $this->user()->company_id),
        ];
    }
}
