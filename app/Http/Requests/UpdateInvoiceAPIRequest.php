<?php

namespace App\Http\Requests;

class UpdateInvoiceAPIRequest extends InvoiceRequest
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

        if ($this->action == ACTION_ARCHIVE) {
            return [];
        }

        $invoiceId = $this->entity()->id;

        return [
            'invoice_items'  => 'valid_invoice_items',
            'invoice_number' => 'unique:invoices,invoice_number,' . $invoiceId . ',id,company_id,' . $this->user()->company_id,
            'discount'       => 'positive',
            //'invoice_date' => 'date',
            //'due_at' => 'date',
            //'start_date' => 'date',
            //'end_date' => 'date',
        ];
    }
}
