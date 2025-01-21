<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use App\Models\PaymentTerm;

class CreatePaymentTermRequest extends PaymentTermRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user()->can('create', PaymentTerm::class)) {
            return true;
        }

        return (bool) $this->user()->can('create', Invoice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{num_days: string}
     */
    public function rules(): array
    {
        return [
            'num_days' => 'required|numeric|unique:payment_terms,num_days,,id,company_id,' . $this->user()->company_id . ',deleted_at,NULL'
                . '|unique:payment_terms,num_days,,id,company_id,0,deleted_at,NULL',
        ];
    }
}
