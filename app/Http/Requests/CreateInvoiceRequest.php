<?php

namespace App\Http\Requests;

use App\Models\Client;
use App\Models\Invoice;

class CreateInvoiceRequest extends InvoiceRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->input('is_quote')) {
            return $this->user()->can('createEntity', ENTITY_QUOTE);
        }

        return $this->user()->can('create', Invoice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'client'         => 'required',
            'invoice_items'  => 'valid_invoice_items',
            'invoice_number' => 'required|unique:invoices,invoice_number,,id,company_id,' . $this->user()->company_id,
            'discount'       => 'positive',
            'invoice_date'   => 'required',
            //'due_at' => 'date',
            //'start_date' => 'date',
            //'end_date' => 'date',
        ];

        if ($this->user()->company->client_number_counter) {
            $clientId = Client::getPrivateId(request()->input('client')['public_id']);
            $rules['client.id_number'] = 'unique:clients,id_number,' . $clientId . ',id,company_id,' . $this->user()->company_id;
        }

        /* There's a problem parsing the dates
        if (Request::get('is_recurring') && Request::get('start_date') && Request::get('end_date')) {
            $rules['end_date'] = 'after' . Request::get('start_date');
        }
        */

        return $rules;
    }
}
