<?php

namespace App\Http\Requests;

class UpdateProductRequest extends ProductRequest
{
    public function authorize(): bool
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules(): array
    {
        return [
            'product_key' => 'required',
        ];
    }
}
