<?php

namespace App\Http\Requests;

class DeleteVendorRequest extends VendorRequest
{
    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [];
    }
}
