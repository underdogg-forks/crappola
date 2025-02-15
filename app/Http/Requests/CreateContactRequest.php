<?php

namespace App\Http\Requests;

class CreateContactRequest extends ContactRequest
{
    public function authorize()
    {
        return $this->user()->can('create', ENTITY_CONTACT);
    }

    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required',
            'client_id'  => 'required',
        ];
    }
}
