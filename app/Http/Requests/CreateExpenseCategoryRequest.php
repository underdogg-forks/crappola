<?php

namespace App\Http\Requests;

class CreateExpenseCategoryRequest extends ExpenseCategoryRequest
{
    // Expenses

    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_EXPENSE_CATEGORY);
    }

    public function rules(): array
    {
        return [
            'name' => sprintf('required|unique:expense_categories,name,,id,account_id,%s', $this->user()->account_id),
        ];
    }
}
