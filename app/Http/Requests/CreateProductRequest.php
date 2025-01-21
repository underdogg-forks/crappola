<?php

namespace App\Http\Requests;

class CreateProductRequest extends ProductRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_PRODUCT);
    }

    public function rules(): array
    {
        return [
            'product_key' => 'required',
        ];
    }
}
