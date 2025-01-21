<?php

namespace App\Http\Requests;

class CreateContactRequest extends ContactRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_CONTACT);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required',
            'client_id'  => 'required',
        ];
    }
}
