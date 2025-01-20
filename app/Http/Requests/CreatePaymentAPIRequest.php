<?php

namespace App\Http\Requests;

use App\Models\Invoice;

class CreatePaymentAPIRequest extends PaymentRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_PAYMENT);
    }

    public function rules(): array
    {
        if ( ! $this->invoice_id || ! $this->amount) {
            return [
                'invoice_id' => 'required|numeric|min:1',
                'amount'     => 'required|numeric',
            ];
        }
        $this->invoice = Invoice::scope($this->invoice_public_id ?: $this->invoice_id)
            ->withArchived()
            ->invoices()
            ->first();
        $invoice = $this->invoice;

        if ( ! $this->invoice) {
            abort(404, 'Invoice was not found');
        }

        $this->merge([
            'invoice_id' => $invoice->id,
            'client_id'  => $invoice->client->id,
        ]);

        $rules = [
            'amount' => 'required|numeric',
        ];

        if ($this->payment_type_id == PAYMENT_TYPE_CREDIT) {
            $rules['payment_type_id'] = 'has_credit:' . $invoice->client->public_id . ',' . $this->amount;
        }

        return $rules;
    }
}
