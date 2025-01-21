<?php

namespace App\Http\Requests;

use App\Models\Invoice;

class CreateInvoiceAPIRequest extends InvoiceRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Invoice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{email: string, client_id: string, invoice_items: string, invoice_number: string, discount: string}
     */
    public function rules(): array
    {
        return [
            'email'          => 'required_without:client_id',
            'client_id'      => 'required_without:email',
            'invoice_items'  => 'valid_invoice_items',
            'invoice_number' => 'unique:invoices,invoice_number,,id,company_id,' . $this->user()->company_id,
            'discount'       => 'positive',
            //'invoice_date' => 'date',
            //'due_at' => 'date',
            //'start_date' => 'date',
            //'end_date' => 'date',
        ];
    }
}
