<?php

namespace App\Http\Requests;

class UpdateProjectRequest extends ProjectRequest
{
    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        if ( ! $this->entity()) {
            return [];
        }

        return [
            'name' => 'required',
        ];
    }
}
