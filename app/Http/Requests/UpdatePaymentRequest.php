<?php

namespace App\Http\Requests;

class UpdatePaymentRequest extends PaymentRequest
{

    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        return [];
    }
}
