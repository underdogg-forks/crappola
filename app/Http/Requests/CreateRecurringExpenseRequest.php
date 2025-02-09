<?php

namespace App\Http\Requests;

class CreateRecurringExpenseRequest extends RecurringExpenseRequest
{
    public function authorize()
    {
        return $this->user()->can('create', ENTITY_RECURRING_EXPENSE);
    }

    public function rules()
    {
        return [
            'amount' => 'numeric',
        ];
    }
}
