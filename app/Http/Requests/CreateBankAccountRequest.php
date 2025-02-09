<?php

namespace App\Http\Requests;

class CreateBankAccountRequest extends Request
{
    // Expenses

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_id'       => 'required',
            'bank_username' => 'required',
            'bank_password' => 'required',
        ];
    }
}
