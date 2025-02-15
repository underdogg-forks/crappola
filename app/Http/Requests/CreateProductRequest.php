<?php

namespace App\Http\Requests;

class CreateProductRequest extends ProductRequest
{
    public function authorize()
    {
        return $this->user()->can('create', ENTITY_PRODUCT);
    }

    public function rules()
    {
        return [
            'product_key' => 'required',
        ];
    }
}
