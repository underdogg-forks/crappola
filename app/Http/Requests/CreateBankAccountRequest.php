<?php

namespace App\Http\Requests;

class CreateBankAccountRequest extends Request
{
    // Expenses

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{bank_id: string, bank_username: string, bank_password: string}
     */
    public function rules(): array
    {
        return [
            'bank_id'       => 'required',
            'bank_username' => 'required',
            'bank_password' => 'required',
        ];
    }
}
