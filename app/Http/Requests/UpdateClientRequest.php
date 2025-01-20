<?php

namespace App\Http\Requests;

class UpdateClientRequest extends ClientRequest
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
     */
    public function rules(): array
    {
        if (! $this->entity()) {
            return [];
        }

        $rules = [];

        if ($this->user()->company->client_number_counter) {
            $rules['id_number'] = 'unique:clients,id_number,' . $this->entity()->id . ',id,company_id,' . $this->user()->company_id;
        }

        return $rules;
    }
}
