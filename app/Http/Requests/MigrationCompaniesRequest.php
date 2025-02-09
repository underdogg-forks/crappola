<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MigrationCompaniesRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'companies' => 'required',
        ];
    }
}
