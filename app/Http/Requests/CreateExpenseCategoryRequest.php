<?php

namespace App\Http\Requests;

class CreateExpenseCategoryRequest extends ExpenseCategoryRequest
{
    // Expenses

    public function authorize()
    {
        return $this->user()->can('create', ENTITY_EXPENSE_CATEGORY);
    }

    public function rules()
    {
        return [
            'name' => sprintf('required|unique:expense_categories,name,,id,account_id,%s', $this->user()->account_id),
        ];
    }
}
