<?php

namespace App\Http\Requests;

class UpdateProductRequest extends ProductRequest
{
    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [
            'product_key' => 'required',
        ];
    }
}
