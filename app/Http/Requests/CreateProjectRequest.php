<?php

namespace App\Http\Requests;

use App\Models\Project;

class CreateProjectRequest extends ProjectRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Project::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{name: string, client_id: string}
     */
    public function rules(): array
    {
        return [
            'name'      => 'required',
            'client_id' => 'required',
        ];
    }
}
