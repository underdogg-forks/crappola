<?php

namespace App\Http\Requests;

class UpdateCreditRequest extends CreditRequest
{
    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [
            'amount' => 'positive',
        ];
    }
}
