<?php

namespace App\Http\Requests;

class CreateTaskRequest extends TaskRequest
{

    public function authorize()
    {
        return $this->user()->can('create', ENTITY_TASK);
    }

    public function rules()
    {
        return [
            'time_log' => 'time_log',
        ];
    }
}
