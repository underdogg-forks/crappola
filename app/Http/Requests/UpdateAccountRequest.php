<?php

namespace App\Http\Requests;

class UpdateAccountRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'logo' => 'sometimes|max:' . MAX_LOGO_FILE_SIZE . '|mimes:jpeg,gif,png',
        ];
    }
}
