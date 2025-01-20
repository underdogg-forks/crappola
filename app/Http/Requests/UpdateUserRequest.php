<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends EntityRequest
{
    // Expenses

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->is_admin || $this->user()->id == Auth::user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{email: string, first_name: string, last_name: string}
     */
    public function rules(): array
    {
        return [
            'email'      => 'email|required|unique:users,email,' . Auth::user()->id . ',id',
            'first_name' => 'required',
            'last_name'  => 'required',
        ];
    }
}
