<?php

namespace App\Http\Requests;

class UpdateContactRequest extends ContactRequest
{
    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required',
        ];
    }
}
