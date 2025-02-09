<?php

namespace App\Http\Requests;

class CreateVendorRequest extends VendorRequest
{

    public function authorize()
    {
        return $this->user()->can('create', ENTITY_VENDOR);
    }

    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
