<?php

namespace App\Http\Requests;

use App\Models\Credit;

class CreateCreditRequest extends CreditRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Credit::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{client_id: string, amount: string}
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required',
            'amount'    => 'required|positive',
        ];
    }
}
