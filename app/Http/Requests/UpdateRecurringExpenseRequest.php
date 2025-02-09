<?php

namespace App\Http\Requests;

class UpdateRecurringExpenseRequest extends RecurringExpenseRequest
{

    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [
            'amount' => 'numeric',
        ];
    }
}
