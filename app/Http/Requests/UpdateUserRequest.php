<?php

namespace App\Http\Requests;

class UpdateUserRequest extends EntityRequest
{
    // Expenses

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()->is_admin || $this->user()->id == \Illuminate\Support\Facades\Auth::user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email'      => 'email|required|unique:users,email,' . \Illuminate\Support\Facades\Auth::user()->id . ',id',
            'first_name' => 'required',
            'last_name'  => 'required',
        ];
    }
}
