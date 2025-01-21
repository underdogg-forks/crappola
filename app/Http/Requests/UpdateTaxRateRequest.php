<?php

namespace App\Http\Requests;

class UpdateTaxRateRequest extends TaxRateRequest
{
    public function authorize(): bool
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'rate' => 'required',
        ];
    }
}
