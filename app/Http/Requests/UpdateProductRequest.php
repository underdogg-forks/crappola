<?php

namespace App\Http\Requests;

class UpdateProductRequest extends ProductRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! $this->entity()) {
            return false;
        }

        return (bool) $this->user()->can('edit', $this->entity());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{product_key: string}
     */
    public function rules(): array
    {
        return [
            'product_key' => 'required',
        ];
    }
}
