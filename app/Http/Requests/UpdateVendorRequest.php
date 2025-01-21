<?php

namespace App\Http\Requests;

class UpdateVendorRequest extends VendorRequest
{
    public function authorize(): bool
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
