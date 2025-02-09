<?php

namespace App\Http\Requests;

class CreateTaxRateRequest extends TaxRateRequest
{
    // Expenses

    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_TAX_RATE);
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'rate' => 'required',
        ];
    }
}
