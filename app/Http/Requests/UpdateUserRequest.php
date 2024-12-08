<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends EntityRequest
{
    // Expenses

    public function authorize(): bool
    {
        return Auth::user()->is_admin || $this->user()->id == Auth::user()->id;
    }

    public function rules(): array
    {
        return [
            'email'      => 'email|required|unique:users,email,' . Auth::user()->id . ',id',
            'first_name' => 'required',
            'last_name'  => 'required',
        ];
    }
}
