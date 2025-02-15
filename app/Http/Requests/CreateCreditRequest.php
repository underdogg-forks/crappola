<?php

namespace App\Http\Requests;

class CreateCreditRequest extends CreditRequest
{
    public function authorize()
    {
        return $this->user()->can('create', ENTITY_CREDIT);
    }

    public function rules()
    {
        return [
            'client_id' => 'required',
            'amount'    => 'required|positive',
        ];
    }
}
