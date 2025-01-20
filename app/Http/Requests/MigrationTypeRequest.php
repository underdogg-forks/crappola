<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MigrationTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'option' => 'required|in:0,1',
        ];
    }
}
