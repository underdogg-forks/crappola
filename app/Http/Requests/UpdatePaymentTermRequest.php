<?php

namespace App\Http\Requests;

class UpdatePaymentTermRequest extends PaymentTermRequest
{
    public function authorize()
    {
        return $this->entity() && $this->user()->can('edit', $this->entity());
    }

    public function rules()
    {
        if ( ! $this->entity()) {
            return [];
        }

        $paymentTermId = $this->entity()->id;

        $rules = [
            'num_days' => 'required|numeric|unique:payment_terms,num_days,' . $paymentTermId . ',id,account_id,' . $this->user()->account_id . ',deleted_at,NULL'
                . '|unique:payment_terms,num_days,' . $paymentTermId . ',id,account_id,0,deleted_at,NULL',
        ];

        return $rules;
    }
}
