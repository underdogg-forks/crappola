<?php

namespace App\Http\Requests;

class CreateCustomerRequest extends CustomerRequest
{
    public function authorize()
    {
        return $this->user()->can('create', ENTITY_CUSTOMER);
    }

    public function rules()
    {
        $rules = [
            'token'                           => 'required',
            'client_id'                       => 'required',
            'contact_id'                      => 'required',
            'payment_method.source_reference' => 'required',
        ];

        return $rules;
    }
}
