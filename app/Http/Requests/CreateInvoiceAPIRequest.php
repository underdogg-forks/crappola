<?php

namespace App\Http\Requests;

class CreateInvoiceAPIRequest extends InvoiceRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ENTITY_INVOICE);
    }

    public function rules(): array
    {
        return [
            'email'          => 'required_without:client_id',
            'client_id'      => 'required_without:email',
            'invoice_items'  => 'valid_invoice_items',
            'invoice_number' => 'unique:invoices,invoice_number,,id,account_id,' . $this->user()->account_id,
            'discount'       => 'positive',
            //'invoice_date' => 'date',
            //'due_date' => 'date',
            //'start_date' => 'date',
            //'end_date' => 'date',
        ];
    }
}
