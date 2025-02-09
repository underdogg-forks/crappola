<?php

namespace App\Http\Requests;

use Illuminate\Http\Request as InputRequest;

class RegisterRequest extends Request
{
    public function __construct(InputRequest $req)
    {
        $this->req = $req;
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'email'      => 'email|required|unique:users',
            'first_name' => 'required',
            'last_name'  => 'required',
            'password'   => 'required',
        ];

        return $rules;
    }
}
