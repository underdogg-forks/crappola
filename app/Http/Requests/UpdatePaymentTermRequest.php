<?php

namespace App\Http\Requests;

class UpdatePaymentTermRequest extends PaymentTermRequest
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
        if (! $this->entity()) {
            return [];
        }

        $paymentTermId = $this->entity()->id;

        return [
            'num_days' => 'required|numeric|unique:payment_terms,num_days,' . $paymentTermId . ',id,company_id,' . $this->user()->company_id . ',deleted_at,NULL'
                . '|unique:payment_terms,num_days,' . $paymentTermId . ',id,company_id,0,deleted_at,NULL',
        ];
    }
}
