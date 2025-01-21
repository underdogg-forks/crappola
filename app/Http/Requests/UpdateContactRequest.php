<?php

namespace App\Http\Requests;

class UpdateContactRequest extends ContactRequest
{
    public function authorize(): bool
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required',
        ];
    }
}
