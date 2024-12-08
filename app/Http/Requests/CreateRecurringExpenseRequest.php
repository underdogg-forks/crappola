<?php

namespace App\Http\Requests;

class CreateRecurringExpenseRequest extends RecurringExpenseRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_RECURRING_EXPENSE);
    }

    public function rules(): array
    {
        return [
            'amount' => 'numeric',
        ];
    }
}
