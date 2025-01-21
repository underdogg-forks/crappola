<?php

namespace App\Http\Requests;

class CreateTaskRequest extends TaskRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_TASK);
    }

    public function rules(): array
    {
        return [
            'time_log' => 'time_log',
        ];
    }
}
