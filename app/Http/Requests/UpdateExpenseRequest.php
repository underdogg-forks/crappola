<?php

namespace App\Http\Requests;

class UpdateExpenseRequest extends ExpenseRequest
{
    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [];
    }
}
