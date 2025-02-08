<?php

namespace App\Ninja\Transformers;

use App\Models\Company;
use Laracasts\Presenter\Exceptions\PresenterException;
use League\Fractal\Resource\Collection;

/**
 * Class AccountTransformer.
 */
class AccountTransformer extends EntityTransformer
{
    /**
     * @SWG\Property(property="account_key", type="string", example="123456")
     * @SWG\Property(property="logo", type="string", example="Logo")
     * @SWG\Property(property="name", type="string", example="John Doe")
     * @SWG\Property(property="id_number", type="string", example="123456")
     * @SWG\Property(property="currency_id", type="integer", example=1)
     * @SWG\Property(property="timezone_id", type="integer", example=1)
     * @SWG\Property(property="date_format_id", type="integer", example=1)
     * @SWG\Property(property="datetime_format_id", type="integer", example=1)
     * @SWG\Property(property="updated_at", type="integer", example=1451160233, readOnly=true)
     * @SWG\Property(property="archived_at", type="integer", example=1451160233, readOnly=true)
     * @SWG\Property(property="address1", type="string", example="10 Main St.")
     * @SWG\Property(property="address2", type="string", example="1st Floor")
     * @SWG\Property(property="city", type="string", example="New York")
     * @SWG\Property(property="state", type="string", example="NY")
     * @SWG\Property(property="postal_code", type="string", example=10010)
     * @SWG\Property(property="country_id", type="integer", example=840)
     * @SWG\Property(property="invoice_terms", type="string", example="Terms")
     * @SWG\Property(property="industry_id", type="integer", example=1)
     * @SWG\Property(property="size_id", type="integer", example=1)
     * @SWG\Property(property="invoice_taxes", type="boolean", example=false)
     * @SWG\Property(property="invoice_item_taxes", type="boolean", example=false)
     * @SWG\Property(property="invoice_design_id", type="integer", example=1)
     * @SWG\Property(property="quote_design_id", type="integer", example=1)
     * @SWG\Property(property="client_view_css", type="string", example="CSS")
     * @SWG\Property(property="work_phone", type="string", example="(212) 555-1212")
     * @SWG\Property(property="work_email", type="string", example="john.doe@companyPlan.com")
     * @SWG\Property(property="language_id", type="integer", example=1)
     * @SWG\Property(property="fill_products", type="boolean", example=false)
     * @SWG\Property(property="update_products", type="boolean", example=false)
     * @SWG\Property(property="vat_number", type="string", example="123456")
     * @SWG\Property(property="custom_value1", type="string", example="Value")
     * @SWG\Property(property="custom_value2", type="string", example="Value")
     * @SWG\Property(property="primary_color", type="string", example="Color")
     * @SWG\Property(property="secondary_color", type="string", example="Color")
     * @SWG\Property(property="hide_quantity", type="boolean", example=false)
     * @SWG\Property(property="hide_paid_to_date", type="boolean", example=false)
     * @SWG\Property(property="invoice_number_prefix", type="string", example="Invoice Number Prefix")
     * @SWG\Property(property="invoice_number_counter", type="integer", example=1)
     * @SWG\Property(property="quote_number_prefix", type="string", example="Quote Number Prefix")
     * @SWG\Property(property="quote_number_counter", type="integer", example=1)
     * @SWG\Property(property="share_counter", type="boolean", example=false)
     * @SWG\Property(property="token_billing_type_id", type="integer", example=1)
     * @SWG\Property(property="invoice_footer", type="string", example="Footer")
     * @SWG\Property(property="pdf_email_attachment", type="boolean", example=false)
     * @SWG\Property(property="font_size", type="string", example="14")
     * @SWG\Property(property="invoice_labels", type="string", example="Labels")
     * @SWG\Property(property="custom_design1", type="string", example="Design")
     * @SWG\Property(property="custom_design2", type="string", example="Design")
     * @SWG\Property(property="custom_design3", type="string", example="Design")
     * @SWG\Property(property="show_item_taxes", type="boolean", example=false)
     * @SWG\Property(property="military_time", type="boolean", example=false)
     * @SWG\Property(property="tax_name1", type="string", example="VAT")
     * @SWG\Property(property="tax_name2", type="string", example="Upkeep")
     * @SWG\Property(property="tax_rate1", type="number", format="float", example="17.5")
     * @SWG\Property(property="tax_rate2", type="number", format="float", example="30.0")
     * @SWG\Property(property="recurring_hour", type="string", example="Recurring Hour")
     * @SWG\Property(property="invoice_number_pattern", type="string", example="Invoice Number Pattern")
     * @SWG\Property(property="quote_number_pattern", type="string", example="Quote Number Pattern")
     * @SWG\Property(property="quote_terms", type="string", example="Labels")
     * @SWG\Property(property="website", type="string", example="http://www.example.com")
     * @SWG\Property(property="header_font_id", type="integer", example=1)
     * @SWG\Property(property="body_font_id", type="integer", example=1)
     * @SWG\Property(property="auto_convert_quote", type="boolean", example=false)
     * @SWG\Property(property="auto_archive_quote", type="boolean", example=false)
     * @SWG\Property(property="auto_archive_invoice", type="boolean", example=false)
     * @SWG\Property(property="auto_email_invoice", type="boolean", example=false)
     * @SWG\Property(property="all_pages_footer", type="boolean", example=false)
     * @SWG\Property(property="all_pages_header", type="boolean", example=false)
     * @SWG\Property(property="show_currency_code", type="boolean", example=false)
     * @SWG\Property(property="enable_portal_password", type="boolean", example=false)
     * @SWG\Property(property="send_portal_password", type="boolean", example=false)
     * @SWG\Property(property="recurring_invoice_number_prefix", type="string", example="Recurring Invoice Number Prefix")
     * @SWG\Property(property="enable_client_portal", type="boolean", example=false)
     * @SWG\Property(property="invoice_fields", type="string", example="Invoice Fields")
     * @SWG\Property(property="invoice_embed_documents", type="boolean", example=false)
     * @SWG\Property(property="document_email_attachment", type="boolean", example=false)
     * @SWG\Property(property="enable_client_portal_dashboard", type="boolean", example=false)
     * @SWG\Property(property="page_size", type="string", example="Page Size")
     * @SWG\Property(property="live_preview", type="boolean", example=false)
     * @SWG\Property(property="invoice_number_padding", type="integer", example=1)
     * @SWG\Property(property="enable_second_tax_rate", type="boolean", example=false)
     * @SWG\Property(property="auto_bill_on_due_date", type="boolean", example=false)
     * @SWG\Property(property="start_of_week", type="string", example="Monday")
     * @SWG\Property(property="enable_buy_now_buttons", type="boolean", example=false)
     * @SWG\Property(property="include_item_taxes_inline", type="boolean", example=false)
     * @SWG\Property(property="financial_year_start", type="string", example="January")
     * @SWG\Property(property="enabled_modules", type="integer", example=1)
     * @SWG\Property(property="enabled_dashboard_sections", type="integer", example=1)
     * @SWG\Property(property="show_accept_invoice_terms", type="boolean", example=false)
     * @SWG\Property(property="show_accept_quote_terms", type="boolean", example=false)
     * @SWG\Property(property="require_invoice_signature", type="boolean", example=false)
     * @SWG\Property(property="require_quote_signature", type="boolean", example=false)
     * @SWG\Property(property="client_number_prefix", type="string", example="Client Number Prefix")
     * @SWG\Property(property="client_number_counter", type="integer", example=1)
     * @SWG\Property(property="client_number_pattern", type="string", example="Client Number Pattern")
     * @SWG\Property(property="payment_terms", type="integer", example=1)
     * @SWG\Property(property="reset_counter_frequency_id", type="integer", example=1)
     * @SWG\Property(property="payment_type_id", type="integer", example=1)
     * @SWG\Property(property="gateway_fee_enabled", type="boolean", example=false)
     * @SWG\Property(property="send_item_details", type="boolean", example=false)
     * @SWG\Property(property="reset_counter_date", type="string", format="date", example="2018-01-01")
     * @SWG\Property(property="task_rate", type="number", format="float", example="17.5")
     * @SWG\Property(property="inclusive_taxes", type="boolean", example=false)
     * @SWG\Property(property="convert_products", type="boolean", example=false)
     * @SWG\Property(property="signature_on_pdf", type="boolean", example=false)
     * @SWG\Property(property="custom_invoice_taxes1", type="string", example="Value")
     * @SWG\Property(property="custom_invoice_taxes2", type="string", example="Value")
     * @SWG\Property(property="custom_fields", type="string", example="Field")
     * @SWG\Property(property="custom_messages", type="string", example="Message")
     * @SWG\Property(property="custom_invoice_label1", type="string", example="Label")
     * @SWG\Property(property="custom_invoice_label2", type="string", example="Label")
     * @SWG\Property(property="custom_client_label1", type="string", example="Label")
     * @SWG\Property(property="custom_client_label2", type="string", example="Label")
     * @SWG\Property(property="custom_contact_label1", type="string", example="Label")
     * @SWG\Property(property="custom_contact_label2", type="string", example="Label")
     * @SWG\Property(property="custom_label1", type="string", example="Label")
     * @SWG\Property(property="custom_label2", type="string", example="Label")
     * @SWG\Property(property="custom_invoice_text_label1", type="string", example="Label")
     * @SWG\Property(property="custom_invoice_text_label2", type="string", example="Label")
     * @SWG\Property(property="custom_invoice_item_label1", type="string", example="Label")
     * @SWG\Property(property="custom_invoice_item_label2", type="string", example="Label")
     */

    /**
     * @var array
     */
    protected array $defaultIncludes = [
        'users',
        'products',
        'tax_rates',
        'expense_categories',
        'projects',
        'account_email_settings',
    ];

    /**
     * @var array
     */
    protected array $availableIncludes = [
        'clients',
        'invoices',
        'payments',
    ];

    /**
     * @return Collection
     */
    public function includeAccountEmailSettings(company $company)
    {
        $transformer = new AccountEmailSettingsTransformer($company, $this->serializer);

        return $this->includeItem($company->account_email_settings, $transformer, 'account_email_settings');
    }

    /**
     * @return Collection
     */
    public function includeExpenseCategories(company $company)
    {
        $transformer = new ExpenseCategoryTransformer($company, $this->serializer);

        return $this->includeCollection($company->expense_categories, $transformer, 'expense_categories');
    }

    /**
     * @return Collection
     */
    public function includeProjects(company $company)
    {
        $transformer = new ProjectTransformer($company, $this->serializer);

        return $this->includeCollection($company->projects, $transformer, 'projects');
    }

    /**
     * @return Collection
     */
    public function includeUsers(company $company)
    {
        $transformer = new UserTransformer($company, $this->serializer);

        return $this->includeCollection($company->users, $transformer, 'users');
    }

    /**
     * @return Collection
     */
    public function includeClients(company $company)
    {
        $transformer = new ClientTransformer($company, $this->serializer);

        return $this->includeCollection($company->clients, $transformer, 'clients');
    }

    /**
     * @return Collection
     */
    public function includeInvoices(company $company)
    {
        $transformer = new InvoiceTransformer($company, $this->serializer);

        return $this->includeCollection($company->invoices, $transformer, 'invoices');
    }

    /**
     * @return Collection
     */
    public function includeProducts(company $company)
    {
        $transformer = new ProductTransformer($company, $this->serializer);

        return $this->includeCollection($company->products, $transformer, 'products');
    }

    /**
     * @return Collection
     */
    public function includeTaxRates(company $company)
    {
        $transformer = new TaxRateTransformer($company, $this->serializer);

        return $this->includeCollection($company->tax_rates, $transformer, 'taxRates');
    }

    /**
     * @return Collection
     */
    public function includePayments(company $company)
    {
        $transformer = new PaymentTransformer($company, $this->serializer);

        return $this->includeCollection($company->payments, $transformer, 'payments');
    }

    /**
     * @return array
     *
     * @throws PresenterException
     */
    public function transform(company $company)
    {
        return [
            'account_key'                     => $company->account_key,
            'logo'                            => $company->logo,
            'name'                            => $company->present()->name,
            'id_number'                       => $company->id_number,
            'currency_id'                     => (int) $company->currency_id,
            'timezone_id'                     => (int) $company->timezone_id,
            'date_format_id'                  => (int) $company->date_format_id,
            'datetime_format_id'              => (int) $company->datetime_format_id,
            'updated_at'                      => $this->getTimestamp($company->updated_at),
            'archived_at'                     => $this->getTimestamp($company->deleted_at),
            'address1'                        => $company->address1,
            'address2'                        => $company->address2,
            'city'                            => $company->city,
            'state'                           => $company->state,
            'postal_code'                     => $company->postal_code,
            'country_id'                      => (int) $company->country_id,
            'invoice_terms'                   => $company->invoice_terms,
            'industry_id'                     => (int) $company->industry_id,
            'size_id'                         => (int) $company->size_id,
            'invoice_taxes'                   => (bool) $company->invoice_taxes,
            'invoice_item_taxes'              => (bool) $company->invoice_item_taxes,
            'invoice_design_id'               => (int) $company->invoice_design_id,
            'quote_design_id'                 => (int) $company->quote_design_id,
            'client_view_css'                 => (string) $company->client_view_css,
            'work_phone'                      => $company->work_phone,
            'work_email'                      => $company->work_email,
            'language_id'                     => (int) $company->language_id,
            'fill_products'                   => (bool) $company->fill_products,
            'update_products'                 => (bool) $company->update_products,
            'vat_number'                      => $company->vat_number,
            'custom_value1'                   => $company->custom_value1,
            'custom_value2'                   => $company->custom_value2,
            'primary_color'                   => $company->primary_color,
            'secondary_color'                 => $company->secondary_color,
            'hide_quantity'                   => (bool) $company->hide_quantity,
            'hide_paid_to_date'               => (bool) $company->hide_paid_to_date,
            'invoice_number_prefix'           => $company->invoice_number_prefix,
            'invoice_number_counter'          => $company->invoice_number_counter,
            'quote_number_prefix'             => $company->quote_number_prefix,
            'quote_number_counter'            => $company->quote_number_counter,
            'share_counter'                   => (bool) $company->share_counter,
            'token_billing_type_id'           => (int) $company->token_billing_type_id,
            'invoice_footer'                  => $company->invoice_footer,
            'pdf_email_attachment'            => (bool) $company->pdf_email_attachment,
            'font_size'                       => $company->font_size,
            'invoice_labels'                  => $company->invoice_labels,
            'custom_design1'                  => $company->custom_design1,
            'custom_design2'                  => $company->custom_design2,
            'custom_design3'                  => $company->custom_design3,
            'show_item_taxes'                 => (bool) $company->show_item_taxes,
            'military_time'                   => (bool) $company->military_time,
            'tax_name1'                       => $company->tax_name1 ?: '',
            'tax_rate1'                       => (float) $company->tax_rate1,
            'tax_name2'                       => $company->tax_name2 ?: '',
            'tax_rate2'                       => (float) $company->tax_rate2,
            'recurring_hour'                  => $company->recurring_hour,
            'invoice_number_pattern'          => $company->invoice_number_pattern,
            'quote_number_pattern'            => $company->quote_number_pattern,
            'quote_terms'                     => $company->quote_terms,
            'enable_email_markup'             => (bool) $company->enable_email_markup,
            'website'                         => $company->website,
            'header_font_id'                  => (int) $company->header_font_id,
            'body_font_id'                    => (int) $company->body_font_id,
            'auto_convert_quote'              => (bool) $company->auto_convert_quote,
            'auto_archive_quote'              => (bool) $company->auto_archive_quote,
            'auto_archive_invoice'            => (bool) $company->auto_archive_invoice,
            'auto_email_invoice'              => (bool) $company->auto_email_invoice,
            'all_pages_footer'                => (bool) $company->all_pages_footer,
            'all_pages_header'                => (bool) $company->all_pages_header,
            'show_currency_code'              => (bool) $company->show_currency_code,
            'enable_portal_password'          => (bool) $company->enable_portal_password,
            'send_portal_password'            => (bool) $company->send_portal_password,
            'recurring_invoice_number_prefix' => $company->recurring_invoice_number_prefix,
            'enable_client_portal'            => (bool) $company->enable_client_portal,
            'invoice_fields'                  => $company->invoice_fields,
            'invoice_embed_documents'         => (bool) $company->invoice_embed_documents,
            'document_email_attachment'       => (bool) $company->document_email_attachment,
            'enable_client_portal_dashboard'  => (bool) $company->enable_client_portal_dashboard,
            'page_size'                       => $company->page_size,
            'live_preview'                    => (bool) $company->live_preview,
            'realtime_preview'                => (bool) $company->realtime_preview,
            'invoice_number_padding'          => (int) $company->invoice_number_padding,
            'enable_second_tax_rate'          => (bool) $company->enable_second_tax_rate,
            'auto_bill_on_due_date'           => (bool) $company->auto_bill_on_due_date,
            'start_of_week'                   => $company->start_of_week,
            'enable_buy_now_buttons'          => (bool) $company->enable_buy_now_buttons,
            'include_item_taxes_inline'       => (bool) $company->include_item_taxes_inline,
            'financial_year_start'            => $company->financial_year_start,
            'enabled_modules'                 => (int) $company->enabled_modules,
            'enabled_dashboard_sections'      => (int) $company->enabled_dashboard_sections,
            'show_accept_invoice_terms'       => (bool) $company->show_accept_invoice_terms,
            'show_accept_quote_terms'         => (bool) $company->show_accept_quote_terms,
            'require_invoice_signature'       => (bool) $company->require_invoice_signature,
            'require_quote_signature'         => (bool) $company->require_quote_signature,
            'client_number_prefix'            => $company->client_number_prefix,
            'client_number_counter'           => (int) $company->client_number_counter,
            'client_number_pattern'           => $company->client_number_pattern,
            'payment_terms'                   => (int) $company->payment_terms,
            'reset_counter_frequency_id'      => (int) $company->reset_counter_frequency_id,
            'payment_type_id'                 => (int) $company->payment_type_id,
            'gateway_fee_enabled'             => (bool) $company->gateway_fee_enabled,
            'send_item_details'               => (bool) $company->send_item_details,
            'reset_counter_date'              => $company->reset_counter_date,
            'task_rate'                       => (float) $company->task_rate,
            'inclusive_taxes'                 => (bool) $company->inclusive_taxes,
            'convert_products'                => (bool) $company->convert_products,
            'signature_on_pdf'                => (bool) $company->signature_on_pdf,
            'custom_invoice_taxes1'           => $company->custom_invoice_taxes1,
            'custom_invoice_taxes2'           => $company->custom_invoice_taxes1,
            'custom_fields'                   => $company->custom_fields,
            'custom_messages'                 => $company->custom_messages,
            'custom_invoice_label1'           => $company->customLabel('invoice1'),
            'custom_invoice_label2'           => $company->customLabel('invoice2'),
            'custom_client_label1'            => $company->customLabel('client1'),
            'custom_client_label2'            => $company->customLabel('client2'),
            'custom_contact_label1'           => $company->customLabel('contact1'),
            'custom_contact_label2'           => $company->customLabel('contact2'),
            'custom_label1'                   => $company->customLabel('account1'),
            'custom_label2'                   => $company->customLabel('account2'),
            'custom_invoice_text_label1'      => $company->customLabel('invoice_text1'),
            'custom_invoice_text_label2'      => $company->customLabel('invoice_text2'),
            'custom_invoice_item_label1'      => $company->customLabel('product1'),
            'custom_invoice_item_label2'      => $company->customLabel('product2'),
        ];
    }
}
