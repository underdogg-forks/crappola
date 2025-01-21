<?php

namespace App\Http\Requests;

class CreateClientRequest extends ClientRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [];

        if ($this->user()->company->client_number_counter) {
            $rules['id_number'] = 'unique:clients,id_number,,id,company_id,' . $this->user()->company_id;
        }

        return $rules;
    }
}
