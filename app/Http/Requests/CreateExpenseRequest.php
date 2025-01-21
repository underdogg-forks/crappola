<?php

namespace App\Http\Requests;

class CreateExpenseRequest extends ExpenseRequest
{
    // Expenses

    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_EXPENSE);
    }

    public function rules(): array
    {
        return [];
    }
}
