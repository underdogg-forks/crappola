<?php

namespace App\Http\Requests;

class CreateCustomerRequest extends CustomerRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'token'                           => 'required',
            'client_id'                       => 'required',
            'contact_id'                      => 'required',
            'payment_method.source_reference' => 'required',
        ];
    }
}
