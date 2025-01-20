<?php

namespace App\Http\Requests;

use Illuminate\Http\Request as InputRequest;

class RegisterRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function __construct(InputRequest $req)
    {
        $this->req = $req;
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{email: string, first_name: string, last_name: string, password: string}
     */
    public function rules(): array
    {
        return [
            'email'      => 'email|required|unique:users',
            'first_name' => 'required',
            'last_name'  => 'required',
            'password'   => 'required',
        ];
    }
}
