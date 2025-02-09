<?php

namespace App\Http\Requests;

class UpdateTaxRateRequest extends TaxRateRequest
{

    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'rate' => 'required',
        ];
    }
}
