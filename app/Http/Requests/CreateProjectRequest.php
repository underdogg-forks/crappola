<?php

namespace App\Http\Requests;

class CreateProjectRequest extends ProjectRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_PROJECT);
    }

    public function rules(): array
    {
        return [
            'name'      => 'required',
            'client_id' => 'required',
        ];
    }
}
