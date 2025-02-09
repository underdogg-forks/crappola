<?php

namespace App\Http\Requests;

class UpdateExpenseCategoryRequest extends ExpenseCategoryRequest
{

    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        if ( ! $this->entity()) {
            return [];
        }

        return [
            'name' => sprintf('required|unique:expense_categories,name,%s,id,account_id,%s', $this->entity()->id, $this->user()->account_id),
        ];
    }
}
