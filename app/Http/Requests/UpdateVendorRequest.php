<?php

namespace App\Http\Requests;

class UpdateVendorRequest extends VendorRequest
{
    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
