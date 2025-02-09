<?php

namespace App\Http\Requests;

class UpdateTaskRequest extends TaskRequest
{

    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [
            'time_log' => 'time_log',
        ];
    }
}
