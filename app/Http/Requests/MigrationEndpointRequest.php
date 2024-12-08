<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MigrationEndpointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'endpoint' => 'required|url',
        ];
    }
}
