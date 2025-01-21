<?php

namespace App\Http\Requests;

class CreateCreditRequest extends CreditRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_CREDIT);
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required',
            'amount'    => 'required|positive',
        ];
    }
}
