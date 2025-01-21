<?php

namespace App\Http\Requests;

class UpdatePaymentRequest extends PaymentRequest
{
    public function authorize(): bool
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules(): array
    {
        return [];
    }
}
