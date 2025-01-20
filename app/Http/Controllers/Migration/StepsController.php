<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\BaseController;
use App\Libraries\Utils;
use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use ZipArchive;

class StepsController extends BaseController
{
    private $company;

    /**
     * @return Factory|View
     */
    public function start()
    {
        return view('migration.start');
    }

    public function import()
    {
        return view('migration.import');
    }

    /**
     * @return Factory|View
     */
    public function download()
    {
        return view('migration.download');
    }

    /**
     * Handle data downloading for the migration.
     *
     * @return JsonResponse
     */
    public function handleDownload()
    {
        $this->company = Auth::user()->company;

        $date = date('Y-m-d');
        $companyKey = $this->company->account_key;

        $output = fopen('php://output', 'w') or Utils::fatalError();

        $fileName = "{$companyKey}-{$date}-invoiceninja";

        $data = [
            'companyPlan' => $this->getCompanyPlan(),
            'users' => $this->getUsers(),
            'tax_rates' => $this->getTaxRates(),
            'clients' => $this->getClients(),
            'products' => $this->getProducts(),
            'invoices' => $this->getInvoices(),
            'quotes' => $this->getQuotes(),
            'payments' => array_merge($this->getPayments(), $this->getCredits()),
            'credits' => $this->getCreditsNotes(),
        ];

        $file = storage_path("{$fileName}.zip");

        $zip = new ZipArchive();
        $zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('migration.json', json_encode($data));
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Length: ' . filesize($file));
        header("Content-Disposition: attachment; filename={$fileName}.zip");

        readfile($file);
        unlink($file);

        return response()->json($data);
    }

    /**
     * Export companyPlan and map to the v2 fields.
     *
     * @return array
     */
    protected function getCompanyPlan()
    {
        return [
            'company_id' => $this->company->id,
            'industry_id' => $this->company->industry_id,
            'ip' => $this->company->ip,
            'company_key' => $this->company->account_key,
            'logo' => $this->company->logo,
            'convert_products' => $this->company->convert_products,
            'fill_products' => $this->company->fill_products,
            'update_products' => $this->company->update_products,
            'show_product_details' => $this->company->show_product_notes,
            'custom_surcharge_taxes1' => $this->company->custom_invoice_taxes1,
            'custom_surcharge_taxes2' => $this->company->custom_invoice_taxes2,
            'enable_invoice_quantity' => !$this->company->hide_quantity,
            'subdomain' => $this->company->subdomain,
            'size_id' => $this->company->size_id,
            'enable_modules' => $this->company->enabled_modules,
            'custom_fields' => $this->company->custom_fields,
            //'uses_inclusive_taxes' => $this->company->inclusive_taxes,
            'created_at' => $this->company->created_at ? $this->company->created_at->toDateString() : null,
            'updated_at' => $this->company->updated_at ? $this->company->updated_at->toDateString() : null,
            'settings' => $this->getCompanyPlanSettings(),
        ];
    }

    public function getCompanyPlanSettings()
    {
        // In v1: custom_invoice_taxes1 & custom_invoice_taxes2, v2: 'invoice_taxes'. What do to with this?
        // V1: invoice_number_prefix, v2: invoice_number_pattern.. same with quote_number, client_number,

        return [
            'timezone_id' => $this->company->timezone_id,
            'date_format_id' => $this->company->date_format_id,
            'currency_id' => $this->company->currency_id,
            'name' => $this->company->name,
            'address1' => $this->company->address1,
            'address2' => $this->company->address2,
            'city' => $this->company->city,
            'state' => $this->company->state,
            'postal_code' => $this->company->postal_code,
            'country_id' => $this->company->country_id,
            'invoice_terms' => $this->company->invoice_terms,
            'enabled_item_tax_rates' => $this->company->invoice_item_taxes,
            'invoice_design_id' => $this->company->invoice_design_id,
            'phone' => $this->company->work_phone,
            'email' => $this->company->work_email,
            'language_id' => $this->company->language_id,
            'custom_value1' => $this->company->custom_value1,
            'custom_value2' => $this->company->custom_value2,
            'hide_paid_to_date' => $this->company->hide_paid_to_date,
            'vat_number' => $this->company->vat_number,
            'shared_invoice_quote_counter' => $this->company->share_counter, // @verify,
            'id_number' => $this->company->id_number,
            'invoice_footer' => $this->company->invoice_footer,
            'pdf_email_attachment' => $this->company->pdf_email_attachment,
            'font_size' => $this->company->font_size,
            'invoice_labels' => $this->company->invoice_labels,
            'military_time' => $this->company->military_time,
            'invoice_number_pattern' => $this->company->invoice_number_pattern,
            'quote_number_pattern' => $this->company->quote_number_pattern,
            'quote_terms' => $this->company->quote_terms,
            'website' => $this->company->website,
            'auto_convert_quote' => $this->company->auto_convert_quote,
            'all_pages_footer' => $this->company->all_pages_footer,
            'all_pages_header' => $this->company->all_pages_header,
            'show_currency_code' => $this->company->show_currency_code,
            'enable_client_portal_password' => $this->company->enable_portal_password,
            'send_portal_password' => $this->company->send_portal_password,
            'recurring_number_prefix' => $this->company->recurring_invoice_number_prefix, // @verify
            'enable_client_portal' => $this->company->enable_client_portal,
            'invoice_fields' => $this->company->invoice_fields,
            'company_logo' => $this->company->logo,
            'embed_documents' => $this->company->invoice_embed_documents,
            'document_email_attachment' => $this->company->document_email_attachment,
            'enable_client_portal_dashboard' => $this->company->enable_client_portal_dashboard,
            'page_size' => $this->company->page_size,
            'show_accept_invoice_terms' => $this->company->show_accept_invoice_terms,
            'show_accept_quote_terms' => $this->company->show_accept_quote_terms,
            'require_invoice_signature' => $this->company->require_invoice_signature,
            'require_quote_signature' => $this->company->require_quote_signature,
            'client_number_counter' => $this->company->client_number_counter,
            'client_number_pattern' => $this->company->client_number_pattern,
            'payment_terms' => $this->company->payment_terms,
            'reset_counter_frequency_id' => $this->company->reset_counter_frequency_id,
            'payment_type_id' => $this->company->payment_type_id,
            'reset_counter_date' => $this->company->reset_counter_date,
            'tax_name1' => $this->company->tax_name1,
            'tax_rate1' => $this->company->tax_rate1,
            'tax_name2' => $this->company->tax_name2,
            'tax_rate2' => $this->company->tax_rate2,
            'quote_design_id' => $this->company->quote_design_id,
            'credit_number_counter' => $this->company->credit_number_counter,
            'credit_number_pattern' => $this->company->credit_number_pattern,
            'default_task_rate' => $this->company->task_rate,
            'inclusive_taxes' => $this->company->inclusive_taxes,
            'signature_on_pdf' => $this->company->signature_on_pdf,
            'ubl_email_attachment' => $this->company->ubl_email_attachment,
            'auto_archive_invoice' => $this->company->auto_archive_invoice,
            'auto_archive_quote' => $this->company->auto_archive_quote,
            'auto_email_invoice' => $this->company->auto_email_invoice,
        ];
    }

    /**
     * @return array
     */
    public function getUsers()
    {
        $users = User::where('company_id', $this->company->id)
            ->withTrashed()
            ->get();

        $transformed = [];

        foreach ($users as $user) {
            $transformed[] = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'confirmation_code' => $user->confirmation_code,
                'failed_logins' => $user->failed_logins,
                'referral_code' => $user->referral_code,
                'oauth_user_id' => $user->oauth_user_id,
                'oauth_provider_id' => $user->oauth_provider_id,
                'google_2fa_secret' => $user->google_2fa_secret,
                'accepted_terms_version' => $user->accepted_terms_version,
                'password' => $user->password,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at ? $user->created_at->toDateString() : null,
                'updated_at' => $user->updated_at ? $user->updated_at->toDateString() : null,
                'deleted_at' => $user->deleted_at ? $user->deleted_at->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array
     */
    public function getTaxRates()
    {
        $rates = TaxRate::where('company_id', $this->company->id)
            ->withTrashed()
            ->get();

        $transformed = [];

        foreach ($rates as $rate) {
            $transformed[] = [
                'name' => $rate->name,
                'rate' => $rate->rate,
                'company_id' => $rate->company_id,
                'user_id' => $rate->user_id,
                'created_at' => $rate->created_at ? $rate->created_at->toDateString() : null,
                'updated_at' => $rate->updated_at ? $rate->updated_at->toDateString() : null,
                'deleted_at' => $rate->deleted_at ? $rate->deleted_at->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array
     */
    protected function getClients()
    {
        $clients = [];

        foreach ($this->company->clients()->withTrashed()->get() as $client) {
            $clients[] = [
                'id' => $client->id,
                'company_id' => $client->company_id,
                'user_id' => $client->user_id,
                'name' => $client->name,
                'balance' => $client->balance,
                'paid_to_date' => $client->paid_to_date,
                'address1' => $client->address1,
                'address2' => $client->address2,
                'city' => $client->city,
                'state' => $client->state,
                'postal_code' => $client->postal_code,
                'country_id' => $client->country_id,
                'phone' => $client->work_phone,
                'private_notes' => $client->private_notes,
                'website' => $client->website,
                'industry_id' => $client->industry_id,
                'size_id' => $client->size_id,
                'is_deleted' => $client->is_deleted,
                'vat_number' => $client->vat_number,
                'id_number' => $client->id_number,
                'custom_value1' => $client->custom_value1,
                'custom_value2' => $client->custom_value2,
                'shipping_address1' => $client->shipping_address1,
                'shipping_address2' => $client->shipping_address2,
                'shipping_city' => $client->shipping_city,
                'shipping_state' => $client->shipping_state,
                'shipping_postal_code' => $client->shipping_postal_code,
                'shipping_country_id' => $client->shipping_country_id,
                'contacts' => $this->getClientContacts($client->contacts),
            ];
        }

        return $clients;
    }

    /**
     * @param $contacts
     *
     * @return array
     */
    protected function getClientContacts($contacts)
    {
        $transformed = [];

        foreach ($contacts as $contact) {
            $transformed[] = [
                'id' => $contact->id,
                'company_id' => $contact->company_id,
                'user_id' => $contact->user_id,
                'client_id' => $contact->client_id,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'phone' => $contact->phone,
                'custom_value1' => $contact->custom_value1,
                'custom_value2' => $contact->custom_value2,
                'email' => $contact->email,
                'is_primary' => $contact->is_primary,
                'send_invoice' => $contact->send_invoice,
                'confirmed' => $contact->confirmation_token ? true : false,
                'last_login' => $contact->last_login,
                'password' => $contact->password,
                'remember_token' => $contact->remember_token,
                'contact_key' => $contact->contact_key,
            ];
        }

        return $transformed;
    }

    /**
     * @return array
     */
    protected function getProducts()
    {
        $products = Product::where('company_id', $this->company->id)
            ->withTrashed()
            ->get();

        $transformed = [];

        foreach ($products as $product) {
            $transformed[] = [
                'company_id' => $product->company_id,
                'user_id' => $product->user_id,
                'custom_value1' => $product->custom_value1,
                'custom_value2' => $product->custom_value2,
                'product_key' => $product->product_key,
                'notes' => $product->notes,
                'cost' => $product->cost,
                'quantity' => $product->qty,
                'tax_name1' => $product->tax_name1,
                'tax_name2' => $product->tax_name2,
                'tax_rate1' => $product->tax_rate1,
                'tax_rate2' => $product->tax_rate2,
                'created_at' => $product->created_at ? $product->created_at->toDateString() : null,
                'updated_at' => $product->updated_at ? $product->updated_at->toDateString() : null,
                'deleted_at' => $product->deleted_at ? $product->deleted_at->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array
     */
    protected function getInvoices()
    {
        $invoices = [];

        foreach ($this->company->invoices()->where('amount', '>=', '0')->withTrashed()->get() as $invoice) {
            $invoices[] = [
                'id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'user_id' => $invoice->user_id,
                'company_id' => $invoice->company_id,
                'status_id' => $invoice->invoice_status_id,
                'design_id' => $invoice->invoice_design_id,
                'number' => $invoice->invoice_number,
                'discount' => $invoice->discount,
                'is_amount_discount' => $invoice->is_amount_discount ?: false,
                'po_number' => $invoice->po_number,
                'date' => $invoice->invoice_date,
                'last_sent_date' => $invoice->last_sent_date,
                'due_at' => $invoice->due_date,
                'is_deleted' => $invoice->is_deleted,
                'footer' => $invoice->invoice_footer,
                'public_notes' => $invoice->public_notes,
                'private_notes' => $invoice->private_notes,
                'uses_inclusive_taxes' => $this->company->inclusive_taxes,
                'terms' => $invoice->terms,
                'tax_name1' => $invoice->tax_name1,
                'tax_name2' => $invoice->tax_name2,
                'tax_rate1' => $invoice->tax_rate1,
                'tax_rate2' => $invoice->tax_rate2,
                'custom_value1' => $invoice->custom_value1,
                'custom_value2' => $invoice->custom_value2,
                'next_send_date' => null,
                'amount' => $invoice->amount,
                'balance' => $invoice->balance,
                'partial' => $invoice->partial,
                'partial_due_date' => $invoice->partial_due_date,
                'line_items' => $this->getInvoiceItems($invoice->invoice_items),
                'created_at' => $invoice->created_at ? $invoice->created_at->toDateString() : null,
                'updated_at' => $invoice->updated_at ? $invoice->updated_at->toDateString() : null,
                'deleted_at' => $invoice->deleted_at ? $invoice->deleted_at->toDateString() : null,
            ];
        }

        return $invoices;
    }

    /**
     * @param $items
     *
     * @return array
     */
    public function getInvoiceItems($items)
    {
        $transformed = [];

        foreach ($items as $item) {
            $transformed[] = [
                'id' => $item->id,
                'quantity' => $item->qty,
                'cost' => $item->cost,
                'product_key' => $item->product_key,
                'notes' => $item->notes,
                'discount' => $item->discount,
                'tax_name1' => $item->tax_name1,
                'tax_rate1' => $item->tax_rate1,
                'date' => $item->created_at,
                'custom_value1' => $item->custom_value1,
                'custom_value2' => $item->custom_value2,
                'line_item_type_id' => $item->invoice_item_type_id,
            ];
        }

        return $transformed;
    }

    /**
     * @return array
     */
    public function getQuotes()
    {
        $transformed = [];

        $quotes = Invoice::where('company_id', $this->company->id)
            ->where('invoice_type_id', '=', INVOICE_TYPE_QUOTE)
            ->withTrashed()
            ->get();

        foreach ($quotes as $quote) {
            $transformed[] = [
                'id' => $quote->id,
                'client_id' => $quote->client_id,
                'user_id' => $quote->user_id,
                'company_id' => $quote->company_id,
                'status_id' => $quote->invoice_status_id,
                'design_id' => $quote->invoice_design_id,
                'number' => $quote->invoice_number,
                'discount' => $quote->discount,
                'is_amount_discount' => $quote->is_amount_discount ?: false,
                'po_number' => $quote->po_number,
                'date' => $quote->invoice_date,
                'last_sent_date' => $quote->last_sent_date,
                'due_at' => $quote->due_date,
                'is_deleted' => $quote->is_deleted,
                'footer' => $quote->invoice_footer,
                'public_notes' => $quote->public_notes,
                'private_notes' => $quote->private_notes,
                'terms' => $quote->terms,
                'tax_name1' => $quote->tax_name1,
                'tax_name2' => $quote->tax_name2,
                'tax_rate1' => $quote->tax_rate1,
                'tax_rate2' => $quote->tax_rate2,
                'custom_value1' => $quote->custom_value1,
                'custom_value2' => $quote->custom_value2,
                'next_send_date' => null,
                'amount' => $quote->amount,
                'balance' => $quote->balance,
                'partial' => $quote->partial,
                'partial_due_date' => $quote->partial_due_date,
                'created_at' => $quote->created_at ? $quote->created_at->toDateString() : null,
                'updated_at' => $quote->updated_at ? $quote->updated_at->toDateString() : null,
                'deleted_at' => $quote->deleted_at ? $quote->deleted_at->toDateString() : null,
            ];
        }

        return $transformed;
    }

    public function getPayments()
    {
        $transformed = [];

        $payments = Payment::where('company_id', $this->company->id)
            ->withTrashed()
            ->get();

        foreach ($payments as $payment) {
            $transformed[] = [
                'id' => $payment->id,
                'invoices' => [
                    ['invoice_id' => $payment->invoice_id, 'amount' => $payment->amount, 'refunded' => $payment->refunded],
                ],
                'invoice_id' => $payment->invoice_id,
                'company_id' => $payment->company_id,
                'client_id' => $payment->client_id,
                'user_id' => $payment->user_id,
                'client_contact_id' => $payment->contact_id,
                'invitation_id' => $payment->invitation_id,
                'company_gateway_id' => $payment->account_gateway_id,
                'type_id' => $payment->payment_type_id,
                'status_id' => $payment->payment_status_id,
                'amount' => $payment->amount,
                'applied' => $payment->amount,
                'refunded' => $payment->refunded,
                'date' => $payment->payment_date,
                'transaction_reference' => $payment->transaction_reference,
                'payer_id' => $payment->payer_id,
                'is_deleted' => $payment->is_deleted,
                'updated_at' => $payment->updated_at ? $payment->updated_at->toDateString() : null,
                'created_at' => $payment->created_at ? $payment->created_at->toDateString() : null,
                'deleted_at' => $payment->deleted_at ? $payment->deleted_at->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array
     */
    private function getCredits()
    {
        $credits = Credit::where('company_id', $this->company->id)->where('balance', '>', '0')->whereIsDeleted(false)
            ->withTrashed()
            ->get();

        $transformed = [];

        foreach ($credits as $credit) {
            $transformed[] = [
                'client_id' => $credit->client_id,
                'user_id' => $credit->user_id,
                'company_id' => $credit->company_id,
                'is_deleted' => $credit->is_deleted,
                'amount' => $credit->balance,
                'applied' => 0,
                'refunded' => 0,
                'date' => $credit->date,
                'created_at' => $credit->created_at ? $credit->created_at->toDateString() : null,
                'updated_at' => $credit->updated_at ? $credit->updated_at->toDateString() : null,
                'deleted_at' => $credit->deleted_at ? $credit->deleted_at->toDateString() : null,
            ];
        }

        return $transformed;
    }

    private function getCreditsNotes()
    {
        $credits = [];

        foreach ($this->company->invoices()->where('amount', '<', '0')->withTrashed()->get() as $credit) {
            $credits[] = [
                'id' => $credit->id,
                'client_id' => $credit->client_id,
                'user_id' => $credit->user_id,
                'company_id' => $credit->company_id,
                'status_id' => $credit->invoice_status_id,
                'design_id' => $credit->invoice_design_id,
                'number' => $credit->invoice_number,
                'discount' => $credit->discount,
                'is_amount_discount' => $credit->is_amount_discount ?: false,
                'po_number' => $credit->po_number,
                'date' => $credit->invoice_date,
                'last_sent_date' => $credit->last_sent_date,
                'due_at' => $credit->due_date,
                'is_deleted' => $credit->is_deleted,
                'footer' => $credit->invoice_footer,
                'public_notes' => $credit->public_notes,
                'private_notes' => $credit->private_notes,
                'terms' => $credit->terms,
                'tax_name1' => $credit->tax_name1,
                'tax_name2' => $credit->tax_name2,
                'tax_rate1' => $credit->tax_rate1,
                'tax_rate2' => $credit->tax_rate2,
                'custom_value1' => $credit->custom_value1,
                'custom_value2' => $credit->custom_value2,
                'next_send_date' => null,
                'amount' => $credit->amount,
                'balance' => $credit->balance,
                'partial' => $credit->partial,
                'partial_due_date' => $credit->partial_due_date,
                'line_items' => $this->getInvoiceItems($credit->invoice_items),
                'created_at' => $credit->created_at ? $credit->created_at->toDateString() : null,
                'updated_at' => $credit->updated_at ? $credit->updated_at->toDateString() : null,
                'deleted_at' => $credit->deleted_at ? $credit->deleted_at->toDateString() : null,
            ];
        }

        return $credits;
    }
}
