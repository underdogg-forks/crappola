<?php

namespace App\Http\Requests;

class CreateVendorRequest extends VendorRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_VENDOR);
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
