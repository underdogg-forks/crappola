<?php

namespace App\Http\Requests;

class CreateTaxRateRequest extends TaxRateRequest
{
    // Expenses

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasPermission('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{name: string, rate: string}
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'rate' => 'required',
        ];
    }
}
