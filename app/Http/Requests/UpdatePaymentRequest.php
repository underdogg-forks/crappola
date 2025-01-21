<?php

namespace App\Http\Requests;

class UpdatePaymentRequest extends PaymentRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! $this->entity()) {
            return false;
        }

        return (bool) $this->user()->can('edit', $this->entity());
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }
}
