<?php

namespace App\Http\Requests;

class UpdateRecurringExpenseRequest extends RecurringExpenseRequest
{
    public function authorize(): bool
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules(): array
    {
        return [
            'amount' => 'numeric',
        ];
    }
}
