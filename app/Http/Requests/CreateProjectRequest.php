<?php

namespace App\Http\Requests;

class CreateProjectRequest extends ProjectRequest
{
    public function authorize()
    {
        return $this->user()->can('create', ENTITY_PROJECT);
    }

    public function rules()
    {
        return [
            'name'      => 'required',
            'client_id' => 'required',
        ];
    }
}
