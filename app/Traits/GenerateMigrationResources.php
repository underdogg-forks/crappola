<?php

namespace App\Traits;

use App\Libraries\Utils;
use App\Models\AccountGateway;
use App\Models\AccountGatewaySettings;
use App\Models\AccountGatewayToken;
use App\Models\AccountToken;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Credit;
use App\Models\Document;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentTerm;
use App\Models\Product;
use App\Models\Project;
use App\Models\RecurringExpense;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaxRate;
use App\Models\User;
use App\Models\Vendor;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use stdClass;

trait GenerateMigrationResources
{
    protected $account;

    protected $token;

    public function getCompanySettings(): array
    {
        info('get co settings');

        $timezone_id = $this->account->timezone_id ?: 15;

        if ($timezone_id > 57) {
            $timezone_id = (string) ($timezone_id - 1);
        }

        return [
            'entity_send_time'                => 6,
            'auto_bill_date'                  => $this->account->auto_bill_on_due_date ? 'on_due_date' : 'on_send_date',
            'auto_bill'                       => $this->transformAutoBill($this->account->token_billing_id),
            'payment_terms'                   => $this->account->payment_terms ? (string) $this->account->payment_terms : '',
            'timezone_id'                     => $timezone_id,
            'date_format_id'                  => $this->account->date_format_id ? (string) $this->account->date_format_id : '1',
            'currency_id'                     => $this->account->currency_id ? (string) $this->account->currency_id : '1',
            'name'                            => $this->account->name ?: trans('texts.untitled'),
            'address1'                        => $this->account->address1 ?: '',
            'address2'                        => $this->account->address2 ?: '',
            'city'                            => $this->account->city ?: '',
            'state'                           => $this->account->state ?: '',
            'postal_code'                     => $this->account->postal_code ?: '',
            'country_id'                      => $this->account->country_id ? (string) $this->account->country_id : '840',
            'invoice_terms'                   => $this->account->invoice_terms ?: '',
            'enabled_item_tax_rates'          => $this->account->invoice_item_taxes ? 2 : 0,
            'invoice_design_id'               => ($this->account->invoice_design_id ?: (string) $this->account->invoice_design_id) ?: '1',
            'phone'                           => $this->account->work_phone ?: '',
            'email'                           => $this->account->work_email ?: '',
            'language_id'                     => $this->account->language_id ? (string) $this->account->language_id : '1',
            'custom_value1'                   => $this->account->custom_value1 ? (string) $this->account->custom_value1 : '',
            'custom_value2'                   => $this->account->custom_value2 ? (string) $this->account->custom_value2 : '',
            'custom_value3'                   => '',
            'custom_value4'                   => '',
            'hide_paid_to_date'               => $this->account->hide_paid_to_date ? (bool) $this->account->hide_paid_to_date : false,
            'vat_number'                      => $this->account->vat_number ?: '',
            'shared_invoice_quote_counter'    => $this->account->share_counter ? (bool) $this->account->share_counter : true,
            'id_number'                       => $this->account->id_number ?: '',
            'invoice_footer'                  => $this->account->invoice_footer ?: '',
            'pdf_email_attachment'            => $this->account->pdf_email_attachment ? (bool) $this->account->pdf_email_attachment : false,
            'font_size'                       => $this->account->font_size ?: 9,
            'invoice_labels'                  => $this->account->invoice_labels ?: '',
            'military_time'                   => $this->account->military_time ? (bool) $this->account->military_time : false,
            'invoice_number_counter'          => $this->account->invoice_number_counter ?: 0,
            'invoice_number_pattern'          => $this->account->invoice_number_pattern ?: '',
            'quote_number_pattern'            => $this->account->quote_number_pattern ?: '',
            'quote_terms'                     => $this->account->quote_terms ?: '',
            'website'                         => $this->account->website ?: '',
            'auto_convert_quote'              => $this->account->auto_convert_quote ? (bool) $this->account->auto_convert_quote : false,
            'all_pages_footer'                => $this->account->all_pages_footer ? (bool) $this->account->all_pages_footer : true,
            'all_pages_header'                => $this->account->all_pages_header ? (bool) $this->account->all_pages_header : true,
            'show_currency_code'              => $this->account->show_currency_code ? (bool) $this->account->show_currency_code : false,
            'enable_client_portal_password'   => $this->account->enable_portal_password ? (bool) $this->account->enable_portal_password : false,
            'send_portal_password'            => $this->account->send_portal_password ? (bool) $this->account->send_portal_password : false,
            'recurring_number_prefix'         => $this->account->recurring_invoice_number_prefix ?: 'R',
            'enable_client_portal'            => $this->account->enable_client_portal ? (bool) $this->account->enable_client_portal : false,
            'invoice_fields'                  => $this->account->invoice_fields ?: '',
            'company_logo'                    => $this->account->getLogoURL() ?: '',
            'embed_documents'                 => $this->account->invoice_embed_documents ? (bool) $this->account->invoice_embed_documents : false,
            'document_email_attachment'       => $this->account->document_email_attachment ? (bool) $this->account->document_email_attachment : false,
            'enable_client_portal_dashboard'  => $this->account->enable_client_portal_dashboard ? (bool) $this->account->enable_client_portal_dashboard : true,
            'page_size'                       => $this->account->page_size ?: 'A4',
            'show_accept_invoice_terms'       => $this->account->show_accept_invoice_terms ? (bool) $this->account->show_accept_invoice_terms : false,
            'show_accept_quote_terms'         => $this->account->show_accept_quote_terms ? (bool) $this->account->show_accept_quote_terms : false,
            'require_invoice_signature'       => $this->account->require_invoice_signature ? (bool) $this->account->require_invoice_signature : false,
            'require_quote_signature'         => $this->account->require_quote_signature ? (bool) $this->account->require_quote_signature : false,
            'client_number_counter'           => $this->account->client_number_counter ?: 0,
            'client_number_pattern'           => $this->account->client_number_pattern ?: '',
            'payment_number_pattern'          => '',
            'payment_number_counter'          => 0,
            'reset_counter_frequency_id'      => $this->account->reset_counter_frequency_id ? (string) $this->transformFrequencyId($this->account->reset_counter_frequency_id) : '0',
            'payment_type_id'                 => $this->account->payment_type_id ? (string) $this->transformPaymentType($this->account->payment_type_id) : '1',
            'reset_counter_date'              => $this->account->reset_counter_date ?: '',
            'tax_name1'                       => $this->account->tax_name1 ?: '',
            'tax_rate1'                       => $this->account->tax_rate1 ?: 0,
            'tax_name2'                       => $this->account->tax_name2 ?: '',
            'tax_rate2'                       => $this->account->tax_rate2 ?: 0,
            'tax_name3'                       => '',
            'tax_rate3'                       => 0,
            'quote_design_id'                 => $this->account->quote_design_id ? (string) $this->account->quote_design_id : '1',
            'credit_number_counter'           => $this->account->credit_number_counter ?: 0,
            'credit_number_pattern'           => $this->account->credit_number_pattern ?: '',
            'default_task_rate'               => $this->account->task_rate ?: 0,
            'inclusive_taxes'                 => $this->account->inclusive_taxes ? (bool) $this->account->inclusive_taxes : false,
            'signature_on_pdf'                => $this->account->signature_on_pdf ? (bool) $this->account->signature_on_pdf : false,
            'ubl_email_attachment'            => $this->account->ubl_email_attachment ? (bool) $this->account->ubl_email_attachment : false,
            'auto_archive_invoice'            => $this->account->auto_archive_invoice ? (bool) $this->account->auto_archive_invoice : false,
            'auto_archive_quote'              => $this->account->auto_archive_quote ? (bool) $this->account->auto_archive_quote : false,
            'auto_email_invoice'              => $this->account->auto_email_invoice ? (bool) $this->account->auto_email_invoice : false,
            'counter_padding'                 => $this->account->invoice_number_padding ?: 4,
            'reply_to_email'                  => $this->account->account_email_settings->reply_to_email ?: '',
            'bcc_email'                       => $this->account->account_email_settings->bcc_email ?: '',
            'email_subject_invoice'           => $this->account->account_email_settings->email_subject_invoice ?: '',
            'email_subject_quote'             => $this->account->account_email_settings->email_subject_quote ?: '',
            'email_subject_payment'           => $this->account->account_email_settings->email_subject_payment ?: '',
            'email_template_invoice'          => $this->account->account_email_settings->email_template_invoice ?: '',
            'email_template_quote'            => $this->account->account_email_settings->email_template_quote ?: '',
            'email_template_payment'          => $this->account->account_email_settings->email_template_payment ?: '',
            'email_subject_reminder1'         => $this->account->account_email_settings->email_subject_reminder1 ?: '',
            'email_subject_reminder2'         => $this->account->account_email_settings->email_subject_reminder2 ?: '',
            'email_subject_reminder3'         => $this->account->account_email_settings->email_subject_reminder3 ?: '',
            'email_subject_reminder_endless'  => $this->account->account_email_settings->email_subject_reminder4 ?: '',
            'email_template_reminder1'        => $this->account->account_email_settings->email_template_reminder1 ?: '',
            'email_template_reminder2'        => $this->account->account_email_settings->email_template_reminder2 ?: '',
            'email_template_reminder3'        => $this->account->account_email_settings->email_template_reminder3 ?: '',
            'email_template_reminder_endless' => $this->account->account_email_settings->email_template_reminder4 ?: '',
            'late_fee_amount1'                => $this->account->account_email_settings->late_fee1_amount ?: 0,
            'late_fee_amount2'                => $this->account->account_email_settings->late_fee2_amount ?: 0,
            'late_fee_amount3'                => $this->account->account_email_settings->late_fee3_amount ?: 0,
            'late_fee_percent1'               => $this->account->account_email_settings->late_fee1_percent ?: 0,
            'late_fee_percent2'               => $this->account->account_email_settings->late_fee2_percent ?: 0,
            'late_fee_percent3'               => $this->account->account_email_settings->late_fee3_percent ?: 0,
            'enable_reminder1'                => (bool) $this->account->enable_reminder1,
            'enable_reminder2'                => (bool) $this->account->enable_reminder2,
            'enable_reminder3'                => (bool) $this->account->enable_reminder3,
            'enable_reminder_endless'         => (bool) $this->account->enable_reminder4,
            'num_days_reminder1'              => $this->account->num_days_reminder1 ?: 0,
            'num_days_reminder2'              => $this->account->num_days_reminder2 ?: 0,
            'num_days_reminder3'              => $this->account->num_days_reminder3 ?: 0,
            'schedule_reminder1'              => $this->buildReminderString($this->account->direction_reminder1, $this->account->field_reminder1),
            'schedule_reminder2'              => $this->buildReminderString($this->account->direction_reminder2, $this->account->field_reminder2),
            'schedule_reminder3'              => $this->buildReminderString($this->account->direction_reminder3, $this->account->field_reminder3),
            'endless_reminder_frequency_id'   => $this->account->account_email_settings->reset_counter_frequency_id ? $this->transformFrequencyId($this->account->account_email_settings->reset_counter_frequency_id) : 0,
            'email_signature'                 => $this->account->email_footer ?: '',
            'email_style'                     => $this->getEmailStyle($this->account->email_design_id),
            'custom_message_dashboard'        => $this->account->customMessage('dashboard'),
            'custom_message_unpaid_invoice'   => $this->account->customMessage('unpaid_invoice'),
            'custom_message_paid_invoice'     => $this->account->customMessage('paid_invoice'),
            'custom_message_unapproved_quote' => $this->account->customMessage('unapproved_quote'),
        ];
    }

    /**
     * @return array<mixed, array<'company_id'|'created_at'|'deleted_at'|'name'|'rate'|'updated_at'|'user_id', mixed>>
     */
    public function getTaxRates(): array
    {
        $rates = TaxRate::where('account_id', $this->account->id)
            ->withTrashed()
            ->get();

        info('get tax rates => ' . $rates->count());

        $transformed = [];

        foreach ($rates as $rate) {
            $transformed[] = [
                'name'       => $rate->name,
                'rate'       => $rate->rate,
                'company_id' => $rate->account_id,
                'user_id'    => $rate->user_id,
                'created_at' => $rate->created_at ? Carbon::parse($rate->created_at)->toDateString() : null,
                'updated_at' => $rate->updated_at ? Carbon::parse($rate->updated_at)->toDateString() : null,
                'deleted_at' => $rate->deleted_at ? Carbon::parse($rate->deleted_at)->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'accepted_terms_version'|'company_user'|'confirmation_code'|'created_at'|'deleted_at'|'email'|'failed_logins'|'first_name'|'google_2fa_secret'|'id'|'last_name'|'password'|'phone'|'referral_code'|'remember_token'|'updated_at', mixed>>
     */
    public function getUsers(): array
    {
        $users = User::where('account_id', $this->account->id)
            ->withTrashed()
            ->get();

        info('get users ' . $users->count());

        $transformed = [];

        foreach ($users as $user) {
            $transformed[] = [
                'id'                => $user->id,
                'first_name'        => $user->first_name ?: '',
                'last_name'         => $user->last_name ?: '',
                'phone'             => $user->phone ?: '',
                'email'             => str_contains($user->username, '@') ? $user->username : $user->email,
                'confirmation_code' => $user->confirmation_code,
                'failed_logins'     => $user->failed_logins,
                'referral_code'     => $user->referral_code,
                // 'oauth_user_id' => $user->oauth_user_id,
                // 'oauth_provider_id' => $user->oauth_provider_id,
                'google_2fa_secret'      => '',
                'accepted_terms_version' => $user->accepted_terms_version,
                'password'               => $user->password,
                'remember_token'         => $user->remember_token,
                'created_at'             => $user->created_at ? Carbon::parse($user->created_at)->toDateString() : null,
                'updated_at'             => $user->updated_at ? Carbon::parse($user->updated_at)->toDateString() : null,
                'deleted_at'             => $user->deleted_at ? Carbon::parse($user->deleted_at)->toDateString() : null,
                'company_user'           => [],
            ];
        }

        return $transformed;
    }

    /**
     * @return non-empty-array<(int | string), \mixed>[]
     */
    public function getResourceInvitations($items, $resourceKeyId): array
    {
        // info("get resource {$resourceKeyId} invitations");

        $transformed = [];

        foreach ($items as $invitation) {
            $transformed[] = [
                'id'                    => $invitation->id,
                'company_id'            => $invitation->account_id,
                'user_id'               => $invitation->user_id,
                'client_contact_id'     => $invitation->contact_id,
                $resourceKeyId          => $invitation->invoice_id,
                'key'                   => $invitation->invitation_key,
                'transaction_reference' => $invitation->transaction_reference,
                'message_id'            => $invitation->message_id,
                'email_error'           => $invitation->email_error ?: '',
                'signature_base64'      => $invitation->signature_base64,
                'signature_date'        => $invitation->signature_date,
                'sent_date'             => $invitation->sent_date,
                'viewed_date'           => $invitation->viewed_date,
                'opened_date'           => $invitation->opened_date,
                'created_at'            => $invitation->created_at ? Carbon::parse($invitation->created_at)->toDateString() : null,
                'updated_at'            => $invitation->updated_at ? Carbon::parse($invitation->updated_at)->toDateString() : null,
                'deleted_at'            => $invitation->deleted_at ? Carbon::parse($invitation->deleted_at)->toDateString() : null,
                'email_status'          => '',
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'cost'|'custom_value1'|'custom_value2'|'custom_value3'|'custom_value4'|'date'|'discount'|'id'|'notes'|'product_key'|'quantity'|'tax_name1'|'tax_name2'|'tax_name3'|'tax_rate1'|'tax_rate2'|'tax_rate3'|'type_id', mixed>>
     */
    public function getCreditItems($balance): array
    {
        info('get credit items');

        $transformed = [];

        $transformed[] = [
            'id'            => '',
            'quantity'      => (float) 1,
            'cost'          => (float) $balance,
            'product_key'   => trans('texts.balance'),
            'notes'         => trans('texts.credit_balance'),
            'discount'      => 0,
            'tax_name1'     => '',
            'tax_rate1'     => 0,
            'tax_name2'     => '',
            'tax_rate2'     => 0,
            'tax_name3'     => '',
            'tax_rate3'     => 0,
            'date'          => '',
            'custom_value1' => '',
            'custom_value2' => '',
            'custom_value3' => '',
            'custom_value4' => '',
            'type_id'       => '1',
        ];

        return $transformed;
    }

    /**
     * @return array<mixed, array<'cost'|'custom_value1'|'custom_value2'|'custom_value3'|'custom_value4'|'date'|'discount'|'id'|'notes'|'product_key'|'quantity'|'tax_name1'|'tax_name2'|'tax_name3'|'tax_rate1'|'tax_rate2'|'tax_rate3'|'type_id', mixed>>
     */
    public function getInvoiceItems($items): array
    {
        // info("get invoice items");

        $transformed = [];

        foreach ($items as $item) {
            $transformed[] = [
                'id'            => $item->id,
                'quantity'      => (float) $item->qty,
                'cost'          => (float) $item->cost,
                'product_key'   => $item->product_key,
                'notes'         => $item->notes,
                'discount'      => (float) $item->discount,
                'tax_name1'     => (string) $item->tax_name1,
                'tax_rate1'     => (float) $item->tax_rate1,
                'tax_name2'     => (string) $item->tax_name2,
                'tax_rate2'     => (float) $item->tax_rate2,
                'tax_name3'     => (string) '',
                'tax_rate3'     => (float) 0,
                'date'          => Carbon::parse($item->created_at)->toDateString(),
                'custom_value1' => $item->custom_value1 ?: '',
                'custom_value2' => $item->custom_value2 ?: '',
                'custom_value3' => '',
                'custom_value4' => '',
                'type_id'       => (string) $item->invoice_item_type_id,
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'amount'|'balance'|'client_id'|'company_id'|'created_at'|'custom_surcharge1'|'custom_surcharge2'|'custom_surcharge_tax1'|'custom_surcharge_tax2'|'custom_value1'|'custom_value2'|'date'|'deleted_at'|'design_id'|'discount'|'due_date'|'footer'|'id'|'invitations'|'invoice_id'|'is_amount_discount'|'is_deleted'|'last_sent_date'|'line_items'|'next_send_date'|'number'|'partial'|'partial_due_date'|'po_number'|'private_notes'|'public_notes'|'status_id'|'tax_name1'|'tax_name2'|'tax_rate1'|'tax_rate2'|'terms'|'updated_at'|'user_id'|'uses_inclusive_taxes', mixed>>
     */
    public function getQuotes(): array
    {
        $transformed = [];

        $quotes = Invoice::where('account_id', $this->account->id)
            ->where('invoice_type_id', INVOICE_TYPE_QUOTE)
            ->withTrashed()
            ->get();

        info('get quotes => ' . $quotes->count());

        foreach ($quotes as $quote) {
            $transformed[] = [
                'id'                    => $quote->id,
                'client_id'             => $quote->client_id,
                'user_id'               => $quote->user_id,
                'company_id'            => $quote->account_id,
                'status_id'             => $this->transformQuoteStatusId($quote),
                'design_id'             => $this->getDesignId($quote->invoice_design_id),
                'number'                => $quote->invoice_number,
                'discount'              => $quote->discount,
                'is_amount_discount'    => $quote->is_amount_discount ?: false,
                'po_number'             => $quote->po_number ?: '',
                'date'                  => $quote->invoice_date,
                'last_sent_date'        => $quote->last_sent_date,
                'due_date'              => $quote->due_date,
                'uses_inclusive_taxes'  => $this->account->inclusive_taxes,
                'is_deleted'            => (bool) $quote->is_deleted,
                'footer'                => $quote->invoice_footer ?: '',
                'public_notes'          => $quote->public_notes ?: '',
                'private_notes'         => $quote->private_notes ?: '',
                'terms'                 => $quote->terms ?: '',
                'tax_name1'             => $quote->tax_name1,
                'tax_name2'             => $quote->tax_name2,
                'tax_rate1'             => $quote->tax_rate1,
                'tax_rate2'             => $quote->tax_rate2,
                'invoice_id'            => Invoice::getPrivateId($quote->quote_invoice_id),
                'custom_surcharge1'     => $quote->custom_value1 ?: '',
                'custom_surcharge2'     => $quote->custom_value2 ?: '',
                'custom_value1'         => $quote->custom_text_value1 ?: '',
                'custom_value2'         => $quote->custom_text_value2 ?: '',
                'custom_surcharge_tax1' => $quote->custom_taxes1 ?: '',
                'custom_surcharge_tax2' => $quote->custom_taxes2 ?: '',
                'next_send_date'        => null,
                'amount'                => $quote->amount ?: 0,
                'balance'               => $quote->balance ?: 0,
                'partial'               => $quote->partial ?: 0,
                'partial_due_date'      => $quote->partial_due_date,
                'line_items'            => $this->getInvoiceItems($quote->invoice_items),
                'created_at'            => $quote->created_at ? Carbon::parse($quote->created_at)->toDateString() : null,
                'updated_at'            => $quote->updated_at ? Carbon::parse($quote->updated_at)->toDateString() : null,
                'deleted_at'            => $quote->deleted_at ? Carbon::parse($quote->deleted_at)->toDateString() : null,
                'invitations'           => $this->getResourceInvitations($quote->invitations, 'quote_id'),
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'amount'|'applied'|'client_contact_id'|'client_id'|'company_gateway_id'|'company_id'|'created_at'|'currency_id'|'date'|'deleted_at'|'exchange_currency_id'|'exchange_rate'|'id'|'invitation_id'|'invoice_id'|'invoices'|'is_deleted'|'payer_id'|'private_notes'|'refunded'|'status_id'|'transaction_reference'|'type_id'|'updated_at'|'user_id', mixed>>
     */
    public function getPayments(): array
    {
        $transformed = [];

        $payments = Payment::where('account_id', $this->account->id)
            ->where('payment_status_id', '!=', PAYMENT_STATUS_VOIDED)
            ->withTrashed()
            ->get();

        info('get payments => ' . $payments->count());

        foreach ($payments as $payment) {
            $transformed[] = [
                'id'       => $payment->id,
                'invoices' => [
                    ['invoice_id' => $payment->invoice_id, 'amount' => $payment->amount, 'refunded' => $payment->refunded],
                ],
                'invoice_id'            => $payment->invoice_id,
                'company_id'            => $payment->account_id,
                'client_id'             => $payment->client_id,
                'user_id'               => $payment->user_id,
                'client_contact_id'     => $payment->contact_id,
                'invitation_id'         => $payment->invitation_id,
                'company_gateway_id'    => $payment->account_gateway_id,
                'type_id'               => $this->transformPaymentType($payment->payment_type_id),
                'status_id'             => $this->transformPaymentStatus($payment),
                'amount'                => $payment->amount ?: 0,
                'applied'               => $payment->amount ?: 0,
                'refunded'              => $payment->refunded ?: 0,
                'date'                  => $payment->payment_date,
                'transaction_reference' => $payment->transaction_reference ?: '',
                'private_notes'         => $payment->private_notes ?: '',
                'payer_id'              => $payment->payer_id,
                'is_deleted'            => (bool) $payment->is_deleted,
                'exchange_rate'         => $payment->exchange_rate ? number_format((float) $payment->exchange_rate, 6) : null,
                'exchange_currency_id'  => $payment->exchange_currency_id,
                'currency_id'           => $payment->client->currency->id ?? $this->account->currency_id,
                'updated_at'            => $payment->updated_at ? Carbon::parse($payment->updated_at)->toDateString() : null,
                'created_at'            => $payment->created_at ? Carbon::parse($payment->created_at)->toDateString() : null,
                'deleted_at'            => $payment->deleted_at ? Carbon::parse($payment->deleted_at)->toDateString() : null,
            ];
        }

        return $transformed;
    }

    protected function getAccount(): array
    {
        info('get account');

        if ($this->account->account_tokens()->exists()) {
            $this->token = $this->account->account_tokens->first()->token;
        } else {
            $mtoken = AccountToken::createNew();
            $mtoken->name = 'Migration Token';
            $mtoken->token = mb_strtolower(\Illuminate\Support\Str::random(RANDOM_KEY_LENGTH));
            $mtoken->save();

            $this->token = $mtoken->token;
        }

        return [
            'plan'          => $this->account->company->plan,
            'plan_term'     => $this->account->company->plan_term,
            'plan_started'  => $this->account->company->plan_started,
            'plan_paid'     => $this->account->company->plan_paid,
            'plan_expires'  => $this->account->company->plan_expires,
            'trial_started' => $this->account->company->trial_started,
            'trial_plan'    => $this->account->company->trial_plan,
            'plan_price'    => $this->account->company->plan_price,
            'num_users'     => $this->account->company->num_users,
            'utm_source'    => $this->account->company->utm_source,
            'utm_medium'    => $this->account->company->utm_medium,
            'utm_campaign'  => $this->account->company->utm_campaign,
            'utm_term'      => $this->account->company->utm_term,
            'utm_content'   => $this->account->company->utm_content,
            'token'         => $this->token,
        ];
    }

    protected function getCompany(): array
    {
        info('get company');

        $financial_year_start = null;
        if ($this->account->financial_year_start) {
            //2000-02-01 format
            $exploded_date = explode('-', $this->account->financial_year_start);

            $financial_year_start = (int) $exploded_date[1];
        }

        return [
            'first_day_of_week'       => $this->account->start_of_week,
            'first_month_of_year'     => $financial_year_start,
            'version'                 => NINJA_VERSION,
            'referral_code'           => $this->account->referral_code ?: '',
            'account_id'              => $this->account->id,
            'google_analytics_key'    => $this->account->analytics_key ?: '',
            'industry_id'             => $this->account->industry_id,
            'ip'                      => $this->account->ip,
            'company_key'             => $this->account->account_key,
            'convert_products'        => $this->account->convert_products,
            'fill_products'           => $this->account->fill_products,
            'update_products'         => $this->account->update_products,
            'show_product_details'    => $this->account->show_product_notes,
            'custom_surcharge_taxes1' => $this->account->custom_invoice_taxes1,
            'custom_surcharge_taxes2' => $this->account->custom_invoice_taxes2,
            'subdomain'               => $this->account->subdomain,
            'portal_mode'             => 'subdomain',
            'size_id'                 => $this->account->size_id,
            'enable_modules'          => $this->account->enabled_modules,
            'custom_fields'           => $this->account->custom_fields,
            'created_at'              => $this->account->created_at ? $this->account->created_at->toDateString() : null,
            'updated_at'              => $this->account->updated_at ? $this->account->updated_at->toDateString() : null,
            'settings'                => $this->getCompanySettings(),
        ];
    }

    /**
     * @return array<mixed, array<'address1'|'address2'|'balance'|'city'|'company_id'|'contacts'|'country_id'|'created_at'|'custom_value1'|'custom_value2'|'deleted_at'|'id'|'id_number'|'industry_id'|'is_deleted'|'name'|'number'|'paid_to_date'|'phone'|'postal_code'|'private_notes'|'settings'|'shipping_address1'|'shipping_address2'|'shipping_city'|'shipping_country_id'|'shipping_postal_code'|'shipping_state'|'size_id'|'state'|'updated_at'|'user_id'|'vat_number'|'website', mixed>>
     */
    protected function getClients(): array
    {
        $clients = [];

        info('get clients => ' . $this->account->clients()->count());

        foreach ($this->account->clients()->withTrashed()->get() as $client) {
            $number = $client->id_number;
            $id_number = '';

            $clients[] = [
                'id'                   => $client->id,
                'company_id'           => $client->account_id,
                'user_id'              => $client->user_id,
                'name'                 => $client->name,
                'balance'              => $client->balance ?: 0,
                'paid_to_date'         => $client->paid_to_date ?: 0,
                'address1'             => $client->address1,
                'address2'             => $client->address2,
                'city'                 => $client->city,
                'state'                => $client->state,
                'postal_code'          => $client->postal_code,
                'country_id'           => $client->country_id ? (string) $client->country_id : (string) $this->account->country_id,
                'phone'                => $client->work_phone,
                'private_notes'        => $client->private_notes,
                'website'              => $client->website,
                'industry_id'          => $client->industry_id,
                'size_id'              => $client->size_id,
                'is_deleted'           => $client->is_deleted,
                'vat_number'           => $client->vat_number,
                'id_number'            => $id_number,
                'number'               => $number,
                'custom_value1'        => $client->custom_value1,
                'custom_value2'        => $client->custom_value2,
                'shipping_address1'    => $client->shipping_address1,
                'shipping_address2'    => $client->shipping_address2,
                'shipping_city'        => $client->shipping_city,
                'shipping_state'       => $client->shipping_state,
                'shipping_postal_code' => $client->shipping_postal_code,
                'shipping_country_id'  => $client->shipping_country_id,
                'contacts'             => $this->getClientContacts($client),
                'settings'             => $this->getClientSettings($client),
                'created_at'           => $client->created_at ? Carbon::parse($client->created_at)->toDateString() : null,
                'updated_at'           => $client->updated_at ? Carbon::parse($client->updated_at)->toDateString() : null,
                'deleted_at'           => $client->deleted_at ? Carbon::parse($client->deleted_at)->toDateString() : null,
            ];
        }

        return $clients;
    }

    /**
     * @return array<mixed, array<'client_id'|'company_id'|'confirmed'|'contact_key'|'created_at'|'custom_value1'|'custom_value2'|'deleted_at'|'email'|'email_verified_at'|'first_name'|'id'|'is_primary'|'last_login'|'last_name'|'password'|'phone'|'remember_token'|'send_email'|'updated_at'|'user_id', mixed>>
     */
    protected function getClientContacts($client): array
    {
        $contacts = Contact::where('client_id', $client->id)->withTrashed()->get();

        $transformed = [];

        info('Importing contacts => ' . $contacts->count());

        foreach ($contacts as $contact) {
            $transformed[] = [
                'id'                => $contact->id,
                'company_id'        => $contact->account_id,
                'user_id'           => $contact->user_id,
                'client_id'         => $contact->client_id,
                'first_name'        => $contact->first_name ?: '',
                'last_name'         => $contact->last_name ?: '',
                'phone'             => $contact->phone ?: '',
                'custom_value1'     => $contact->custom_value1 ?: '',
                'custom_value2'     => $contact->custom_value2 ?: '',
                'email'             => $contact->email,
                'is_primary'        => (bool) $contact->is_primary,
                'send_email'        => (bool) $contact->send_invoice,
                'confirmed'         => (bool) $contact->confirmation_token,
                'email_verified_at' => $contact->created_at ? Carbon::parse($contact->created_at)->toDateTimeString() : null,
                'last_login'        => $contact->last_login,
                'password'          => $contact->password,
                'remember_token'    => $contact->remember_token,
                'contact_key'       => $contact->contact_key,
                'created_at'        => $contact->created_at ? Carbon::parse($contact->created_at)->toDateString() : null,
                'updated_at'        => $contact->updated_at ? Carbon::parse($contact->updated_at)->toDateString() : null,
                'deleted_at'        => $contact->deleted_at ? Carbon::parse($contact->deleted_at)->toDateString() : null,
            ];
        }

        return $transformed;
    }

    protected function getNinjaToken(): array
    {
        $transformed = [];

        if ( ! Utils::isNinja()) {
            return $transformed;
        }

        $db = DB_NINJA_1;
        $account_id = 20432;

        if ($this->account->id > 1000000) {
            $db = DB_NINJA_2;
            $account_id = 1000002;
        }

        $ninja_client = Client::on($db)->where('public_id', $this->account->id)->where('account_id', $account_id)->first();

        if ( ! $ninja_client) {
            return $transformed;
        }

        $agts = AccountGatewayToken::on($db)->where('client_id', $ninja_client->id)->get();
        $is_default = true;

        if (count($agts) == 0) {
            $transformed[] = [
                'client' => $ninja_client->toArray(),
            ];
        }

        foreach ($agts as $agt) {
            $payment_method = $agt->default_payment_method;

            if ( ! $payment_method) {
                continue;
            }

            $contact = Contact::on($db)->where('id', $payment_method->contact_id)->withTrashed()->first();

            $transformed[] = [
                'id'                         => $payment_method->id,
                'company_id'                 => $this->account->id,
                'client_id'                  => $contact->client_id,
                'token'                      => $payment_method->source_reference,
                'company_gateway_id'         => $agt->account_gateway_id,
                'gateway_customer_reference' => $agt->token,
                'gateway_type_id'            => $payment_method->payment_type->gateway_type_id,
                'is_default'                 => $is_default,
                'meta'                       => $this->convertMeta($payment_method),
                'client'                     => $ninja_client->toArray(),
                'contacts'                   => $contact->client->contacts->toArray(),
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'company_id'|'cost'|'created_at'|'custom_value1'|'custom_value2'|'deleted_at'|'notes'|'price'|'product_key'|'quantity'|'tax_name1'|'tax_name2'|'tax_rate1'|'tax_rate2'|'updated_at'|'user_id', mixed>>
     */
    protected function getProducts(): array
    {
        $products = Product::where('account_id', $this->account->id)
            ->withTrashed()
            ->get();

        info('get products ' . $products->count());

        $transformed = [];

        foreach ($products as $product) {
            $transformed[] = [
                'company_id'    => $product->account_id,
                'user_id'       => $product->user_id,
                'custom_value1' => $product->custom_value1 ?: '',
                'custom_value2' => $product->custom_value2 ?: '',
                'product_key'   => $product->product_key ?: '',
                'notes'         => $product->notes ?: '',
                'price'         => $product->cost ?: 0,
                'cost'          => $product->cost ?: 0,
                'quantity'      => $product->qty ?: 0,
                'tax_name1'     => $product->tax_name1,
                'tax_name2'     => $product->tax_name2,
                'tax_rate1'     => $product->tax_rate1,
                'tax_rate2'     => $product->tax_rate2,
                'created_at'    => $product->created_at ? Carbon::parse($product->created_at)->toDateString() : null,
                'updated_at'    => $product->updated_at ? Carbon::parse($product->updated_at)->toDateString() : null,
                'deleted_at'    => $product->deleted_at ? Carbon::parse($product->deleted_at)->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'amount'|'auto_bill_enabled'|'balance'|'client_id'|'company_id'|'created_at'|'custom_surcharge1'|'custom_surcharge2'|'custom_surcharge_tax1'|'custom_surcharge_tax2'|'custom_value1'|'custom_value2'|'date'|'deleted_at'|'design_id'|'discount'|'due_date'|'footer'|'id'|'invitations'|'is_amount_discount'|'is_deleted'|'last_sent_date'|'line_items'|'next_send_date'|'number'|'partial'|'partial_due_date'|'po_number'|'private_notes'|'public_notes'|'recurring_id'|'status_id'|'tax_name1'|'tax_name2'|'tax_rate1'|'tax_rate2'|'terms'|'updated_at'|'user_id'|'uses_inclusive_taxes', mixed>>
     */
    protected function getInvoices(): array
    {
        $invoices = [];

        $export_invoices = Invoice::where('account_id', $this->account->id)
            // ->where('amount', '>=', 0)
            ->where('invoice_type_id', INVOICE_TYPE_STANDARD)
            ->where('is_recurring', false)
            ->withTrashed()
            ->get();

        info('get invoices -> ' . $export_invoices->count());

        foreach ($export_invoices as $invoice) {
            $invoices[] = [
                'id'                   => $invoice->id,
                'client_id'            => $invoice->client_id,
                'user_id'              => $invoice->user_id,
                'company_id'           => $invoice->account_id,
                'status_id'            => $this->transformStatusId($invoice->invoice_status_id, $invoice->is_public),
                'design_id'            => $this->getDesignId($invoice->invoice_design_id),
                'number'               => $invoice->invoice_number,
                'discount'             => $invoice->discount,
                'is_amount_discount'   => $invoice->is_amount_discount ?: false,
                'po_number'            => $invoice->po_number ?: '',
                'date'                 => $invoice->invoice_date,
                'last_sent_date'       => $invoice->last_sent_date,
                'due_date'             => $invoice->due_date,
                'uses_inclusive_taxes' => $this->account->inclusive_taxes,
                'is_deleted'           => $invoice->is_deleted,
                'footer'               => $invoice->invoice_footer ?: '',
                'public_notes'         => $invoice->public_notes ?: '',
                'private_notes'        => $invoice->private_notes ?: '',
                // 'has_tasks' => $invoice->has_tasks,
                // 'has_expenses' => $invoice->has_expenses,
                'terms'                 => $invoice->terms ?: '',
                'tax_name1'             => $invoice->tax_name1,
                'tax_name2'             => $invoice->tax_name2,
                'tax_rate1'             => $invoice->tax_rate1,
                'tax_rate2'             => $invoice->tax_rate2,
                'custom_surcharge1'     => $invoice->custom_value1 ?: '',
                'custom_surcharge2'     => $invoice->custom_value2 ?: '',
                'custom_value1'         => $invoice->custom_text_value1 ?: '',
                'custom_value2'         => $invoice->custom_text_value2 ?: '',
                'custom_surcharge_tax1' => $invoice->custom_taxes1 ?: '',
                'custom_surcharge_tax2' => $invoice->custom_taxes2 ?: '',
                'next_send_date'        => null,
                'amount'                => $invoice->amount ?: 0,
                'balance'               => $invoice->balance ?: 0,
                'partial'               => $invoice->partial ?: 0,
                'partial_due_date'      => $invoice->partial_due_date,
                'line_items'            => $this->getInvoiceItems($invoice->invoice_items),
                'created_at'            => $invoice->created_at ? Carbon::parse($invoice->created_at)->toDateString() : null,
                'updated_at'            => $invoice->updated_at ? Carbon::parse($invoice->updated_at)->toDateString() : null,
                'deleted_at'            => $invoice->deleted_at ? Carbon::parse($invoice->deleted_at)->toDateString() : null,
                'invitations'           => $this->getResourceInvitations($invoice->invitations, 'invoice_id'),
                'auto_bill_enabled'     => $invoice->auto_bill,
                'recurring_id'          => $invoice->recurring_invoice_id,
            ];
        }

        return $invoices;
    }

    /**
     * @return array<mixed, array<'amount'|'category_id'|'client_id'|'company_id'|'created_at'|'currency_id'|'custom_value1'|'custom_value2'|'custom_value3'|'custom_value4'|'deleted_at'|'frequency_id'|'id'|'invoice_currency_id'|'is_deleted'|'next_send_date'|'private_notes'|'public_notes'|'remaining_cycles'|'should_be_invoiced'|'tax_name1'|'tax_name2'|'tax_name3'|'tax_rate1'|'tax_rate2'|'tax_rate3'|'updated_at'|'user_id'|'vendor_id', mixed>>
     */
    protected function getRecurringExpenses(): array
    {
        $expenses = [];

        $export_expenses = RecurringExpense::where('account_id', $this->account->id)
            ->withTrashed()
            ->get();

        info('get recurring Expenses => ' . $export_expenses->count());

        foreach ($export_expenses as $expense) {
            $expenses[] = [
                'id'                  => $expense->id,
                'amount'              => $expense->amount,
                'company_id'          => $this->account->id,
                'client_id'           => $expense->client_id,
                'user_id'             => $expense->user_id,
                'custom_value1'       => '',
                'custom_value2'       => '',
                'custom_value3'       => '',
                'custom_value4'       => '',
                'category_id'         => $expense->expense_category_id,
                'currency_id'         => $expense->expense_currency_id,
                'frequency_id'        => $this->transformFrequencyId($expense->frequency_id),
                'invoice_currency_id' => $expense->invoice_currency_id,
                'private_notes'       => $expense->private_notes,
                'public_notes'        => $expense->public_notes,
                'should_be_invoiced'  => $expense->should_be_invoiced,
                'tax_name1'           => $expense->tax_name1,
                'tax_name2'           => $expense->tax_name2,
                'tax_name3'           => '',
                'tax_rate1'           => $expense->tax_rate1,
                'tax_rate2'           => $expense->tax_rate2,
                'tax_rate3'           => 0,
                'vendor_id'           => $expense->vendor_id,
                'is_deleted'          => $expense->is_deleted,
                'next_send_date'      => $this->getNextSendDateForMigration($expense),
                'remaining_cycles'    => $this->getRemainingCycles($expense),
                'created_at'          => $expense->created_at ? Carbon::parse($expense->created_at)->toDateString() : null,
                'updated_at'          => $expense->updated_at ? Carbon::parse($expense->updated_at)->toDateString() : null,
                'deleted_at'          => $expense->deleted_at ? Carbon::parse($expense->deleted_at)->toDateString() : null,
            ];
        }

        return $expenses;
    }

    /**
     * @return array<mixed, array<'amount'|'auto_bill'|'auto_bill_enabled'|'balance'|'client_id'|'company_id'|'created_at'|'custom_value1'|'custom_value2'|'custom_value3'|'custom_value4'|'date'|'deleted_at'|'design_id'|'discount'|'due_date'|'due_date_days'|'footer'|'frequency_id'|'id'|'invitations'|'is_amount_discount'|'is_deleted'|'last_sent_date'|'line_items'|'next_send_date'|'number'|'partial'|'partial_due_date'|'po_number'|'private_notes'|'public_notes'|'remaining_cycles'|'status_id'|'tax_name1'|'tax_name2'|'tax_name3'|'tax_rate1'|'tax_rate2'|'tax_rate3'|'terms'|'updated_at'|'user_id'|'uses_inclusive_taxes', mixed>>
     */
    protected function getRecurringInvoices(): array
    {
        $invoices = [];

        $export_invoices = Invoice::where('account_id', $this->account->id)
            ->where('amount', '>=', 0)
            ->where('is_recurring', true)
            ->withTrashed()
            ->get();

        info('get recurring invoices => ' . $export_invoices->count());

        foreach ($export_invoices as $invoice) {
            $invoices[] = [
                'id'                   => $invoice->id,
                'client_id'            => $invoice->client_id,
                'user_id'              => $invoice->user_id,
                'company_id'           => $invoice->account_id,
                'status_id'            => $this->transformRecurringStatusId($invoice),
                'design_id'            => $this->getDesignId($invoice->invoice_design_id),
                'number'               => '',
                'discount'             => $invoice->discount,
                'is_amount_discount'   => $invoice->is_amount_discount ?: false,
                'po_number'            => $invoice->po_number ?: '',
                'date'                 => $invoice->invoice_date,
                'last_sent_date'       => $invoice->last_sent_date,
                'due_date'             => $invoice->due_date,
                'uses_inclusive_taxes' => $this->account->inclusive_taxes,
                'is_deleted'           => (bool) $invoice->is_deleted,
                'footer'               => $invoice->invoice_footer,
                'public_notes'         => $invoice->public_notes ?: '',
                'private_notes'        => $invoice->private_notes ?: '',
                'terms'                => $invoice->terms ?: '',
                'tax_name1'            => $invoice->tax_name1,
                'tax_name2'            => $invoice->tax_name2,
                'tax_rate1'            => $invoice->tax_rate1,
                'tax_rate2'            => $invoice->tax_rate2,
                'tax_name3'            => '',
                'tax_rate3'            => 0,
                'custom_value1'        => $invoice->custom_value1 ?: '',
                'custom_value2'        => $invoice->custom_value2 ?: '',
                'custom_value3'        => '',
                'custom_value4'        => '',
                'amount'               => $invoice->amount ?: 0,
                'balance'              => $invoice->balance ?: 0,
                'partial'              => $invoice->partial ?: 0,
                'partial_due_date'     => $invoice->partial_due_date,
                'line_items'           => $this->getInvoiceItems($invoice->invoice_items),
                'created_at'           => $invoice->created_at ? Carbon::parse($invoice->created_at)->toDateString() : null,
                'updated_at'           => $invoice->updated_at ? Carbon::parse($invoice->updated_at)->toDateString() : null,
                'deleted_at'           => $invoice->deleted_at ? Carbon::parse($invoice->deleted_at)->toDateString() : null,
                'next_send_date'       => $this->getNextSendDateForMigration($invoice),
                'frequency_id'         => $this->transformFrequencyId($invoice->frequency_id),
                'due_date_days'        => $this->transformDueDate($invoice),
                'remaining_cycles'     => $this->getRemainingCycles($invoice),
                'invitations'          => $this->getResourceInvitations($invoice->invitations, 'recurring_invoice_id'),
                'auto_bill_enabled'    => $this->calcAutoBill($invoice),
                'auto_bill'            => $this->calcAutoBillEnabled($invoice),
            ];
        }

        return $invoices;
    }

    /**
     * @return array<mixed, array<'address1'|'address2'|'city'|'company_id'|'contacts'|'country_id'|'created_at'|'currency_id'|'custom_value1'|'custom_value2'|'custom_value3'|'custom_value4'|'deleted_at'|'id'|'id_number'|'is_deleted'|'name'|'number'|'phone'|'postal_code'|'private_notes'|'state'|'transaction_name'|'updated_at'|'user_id'|'vat_number'|'website', mixed>>
     */
    protected function getVendors(): array
    {
        $vendor_query = Vendor::where('account_id', $this->account->id)->withTrashed()->get();

        info('get vendors => ' . $vendor_query->count());

        $vendors = [];

        foreach ($vendor_query as $vendor) {
            $vendors[] = [
                'id'          => $vendor->id,
                'company_id'  => $vendor->account_id,
                'user_id'     => $vendor->user_id,
                'name'        => $vendor->name,
                'currency_id' => $vendor->currency_id ? (string) $vendor->currency_id : (string) $this->account->currency_id,
                //'balance' => $vendor->balance ?: 0,
                //'paid_to_date' => $vendor->paid_to_date ?: 0,
                'address1'      => $vendor->address1,
                'address2'      => $vendor->address2,
                'city'          => $vendor->city,
                'state'         => $vendor->state,
                'postal_code'   => $vendor->postal_code,
                'country_id'    => $vendor->country_id,
                'phone'         => $vendor->work_phone,
                'private_notes' => $vendor->private_notes,
                'website'       => $vendor->website,
                //'industry_id' => $vendor->industry_id,
                //'size_id' => $vendor->size_id,
                'is_deleted'       => $vendor->is_deleted,
                'vat_number'       => $vendor->vat_number,
                'id_number'        => null,
                'number'           => $vendor->id_number,
                'custom_value1'    => $vendor->custom_value1,
                'custom_value2'    => $vendor->custom_value2,
                'custom_value3'    => '',
                'custom_value4'    => '',
                'transaction_name' => '',
                'contacts'         => $this->getVendorContacts($vendor->vendor_contacts),
                'created_at'       => $vendor->created_at ? Carbon::parse($vendor->created_at)->toDateString() : null,
                'updated_at'       => $vendor->updated_at ? Carbon::parse($vendor->updated_at)->toDateString() : null,
                'deleted_at'       => $vendor->deleted_at ? Carbon::parse($vendor->deleted_at)->toDateString() : null,
            ];
        }

        return $vendors;
    }

    /**
     * @return array<mixed, array<'company_id'|'confirmed'|'created_at'|'custom_value1'|'custom_value2'|'custom_value3'|'custom_value4'|'deleted_at'|'email'|'email_verified_at'|'first_name'|'id'|'is_locked'|'is_primary'|'last_login'|'last_name'|'password'|'phone'|'send_email'|'updated_at'|'user_id'|'vendor_id', mixed>>
     */
    protected function getVendorContacts($contacts): array
    {
        info('get vendor contacts => ' . $contacts->count());

        $transformed = [];

        foreach ($contacts as $contact) {
            $transformed[] = [
                'id'                => $contact->id,
                'company_id'        => $contact->account_id,
                'user_id'           => $contact->user_id,
                'vendor_id'         => $contact->vendor_id,
                'first_name'        => $contact->first_name ?: '',
                'last_name'         => $contact->last_name ?: '',
                'phone'             => $contact->phone ?: '',
                'custom_value1'     => $contact->custom_value1 ?: '',
                'custom_value2'     => $contact->custom_value2 ?: '',
                'custom_value3'     => '',
                'custom_value4'     => '',
                'email'             => $contact->email,
                'is_primary'        => (bool) $contact->is_primary,
                'send_email'        => (bool) $contact->send_invoice ?: true,
                'confirmed'         => (bool) $contact->confirmation_token,
                'email_verified_at' => $contact->created_at->toDateTimeString(),
                'last_login'        => $contact->last_login,
                'password'          => $contact->password ?: '',
                'is_locked'         => false,
                'confirmed'         => true,
                'created_at'        => $contact->created_at ? Carbon::parse($contact->created_at)->toDateString() : null,
                'updated_at'        => $contact->updated_at ? Carbon::parse($contact->updated_at)->toDateString() : null,
                'deleted_at'        => $contact->deleted_at ? Carbon::parse($contact->deleted_at)->toDateString() : null,
                // 'remember_token' => $contact->remember_token,
                // 'contact_key' => $contact->contact_key,
            ];
        }

        return $transformed;
    }

    /**
     *     define('TOKEN_BILLING_DISABLED', 1);
     *     define('TOKEN_BILLING_OPT_IN', 2);
     *     define('TOKEN_BILLING_OPT_OUT', 3);
     *     define('TOKEN_BILLING_ALWAYS', 4);.
     *
     *     off,always,optin,optout
     */
    private function transformAutoBill($token_billing_id): string
    {
        return match ($token_billing_id) {
            TOKEN_BILLING_DISABLED => 'off',
            TOKEN_BILLING_OPT_IN   => 'optin',
            TOKEN_BILLING_OPT_OUT  => 'optout',
            TOKEN_BILLING_ALWAYS   => 'always',
            default                => 'off',
        };
    }

    private function getEmailStyle($id): string
    {
        return match ($id) {
            1       => 'plain',
            2       => 'light',
            3       => 'dark',
            default => 'light',
        };
    }

    private function buildReminderString($direction, $field): string
    {
        $direction_string = $direction == 1 ? 'after_' : 'before_';
        $field_string = $field == 1 ? 'due_date' : 'invoice_date';

        return $direction_string . $field_string;
    }

    private function getClientSettings($client): stdClass
    {
        $settings = new stdClass();
        $settings->currency_id = $client->currency_id ? (string) $client->currency_id : (string) $client->account->currency_id;

        if ($client->task_rate && $client->task_rate > 0) {
            $settings->default_task_rate = (float) $client->task_rate;
        }

        if ($client->language_id) {
            $settings->language_id = (string) $client->language_id;
        }

        return $settings;
    }

    /**
     * @return array<mixed, array<'amount'|'balance'|'client_id'|'company_id'|'created_at'|'custom_value1'|'custom_value2'|'date'|'deleted_at'|'design_id'|'discount'|'due_date'|'footer'|'id'|'is_amount_discount'|'is_deleted'|'last_sent_date'|'line_items'|'next_send_date'|'number'|'partial'|'partial_due_date'|'po_number'|'private_notes'|'public_notes'|'status_id'|'tax_name1'|'tax_name2'|'tax_name3'|'tax_rate1'|'tax_rate2'|'tax_rate3'|'terms'|'updated_at'|'user_id'|'uses_inclusive_taxes', mixed>>
     */
    private function getCreditsNotes(): array
    {
        $credits = [];
        $export_credits = collect([]);

        $export_credits = Credit::where('account_id', $this->account->id)->where('amount', '>', 0)->whereIsDeleted(false)
            ->withTrashed()
            ->get();

        info('get credit notes => ' . $export_credits->count());

        foreach ($export_credits as $credit) {
            $credits[] = [
                'id'                   => $credit->id,
                'client_id'            => $credit->client_id,
                'user_id'              => $credit->user_id,
                'company_id'           => $credit->account_id,
                'status_id'            => 2,
                'design_id'            => 2,
                'number'               => $credit->credit_number ?: null,
                'discount'             => 0,
                'is_amount_discount'   => 0,
                'po_number'            => '',
                'date'                 => $credit->date,
                'last_sent_date'       => null,
                'due_date'             => null,
                'uses_inclusive_taxes' => $this->account->inclusive_taxes,
                'is_deleted'           => $credit->is_deleted,
                'footer'               => '',
                'public_notes'         => $credit->public_notes,
                'private_notes'        => $credit->private_notes,
                'terms'                => '',
                'tax_name1'            => '',
                'tax_name2'            => '',
                'tax_rate1'            => 0,
                'tax_rate2'            => 0,
                'tax_name3'            => '',
                'tax_rate3'            => 0,
                'custom_value1'        => '',
                'custom_value2'        => '',
                'next_send_date'       => null,
                'amount'               => $credit->amount ? $credit->amount * -1 : 0,
                'balance'              => $credit->balance ? $credit->balance * -1 : 0,
                'partial'              => 0,
                'partial_due_date'     => null,
                'line_items'           => $this->getCreditItems($credit->balance),
                'created_at'           => $credit->created_at ? Carbon::parse($credit->created_at)->toDateString() : null,
                'updated_at'           => $credit->updated_at ? Carbon::parse($credit->updated_at)->toDateString() : null,
                'deleted_at'           => $credit->deleted_at ? Carbon::parse($credit->deleted_at)->toDateString() : null,
            ];
        }

        return $credits;
    }

    // We cant migrate custom designs
    private function getDesignId($design_id)
    {
        if ($design_id >= 11) {
            return 2;
        }

        if ($design_id == 1) {
            return 2;
        }

        if ($design_id == 2) {
            return 3;
        }

        if ($design_id == 3) {
            return 4;
        }

        if ($design_id == 4) {
            return 1;
        }

        if ($design_id == 10) {
            return 2;
        }

        return $design_id;
    }

    private function calcAutoBillEnabled($invoice): string
    {
        if ($invoice->auto_bill === 1) {
            return 'off';
        }

        if ($invoice->auto_bill === 2) {
            return 'optin';
        }

        if ($invoice->auto_bill === 3) {
            return 'optout';
        }

        if ($invoice->auto_bill === 4) {
            return 'always';
        }

        return 'off';
    }

    private function calcAutoBill($invoice)
    {
        if ($invoice->auto_bill == 4) {
            return 1;
        }

        return $invoice->client_enable_auto_bill;
    }

    private function getNextSendDateForMigration($invoice)
    {
        if ($next_send_date = $invoice->getNextSendDate()) {
            return $next_send_date->format('Y-m-d');
        }
    }

    // Determine the number of remaining cycles
    private function getRemainingCycles($invoice): int|float
    {
        if (empty($invoice->end_date) || ! $invoice->end_date) {
            return -1;
        }

        $start_date = $invoice->getNextSendDate();
        $end_date = Carbon::parse($invoice->end_date);

        //v4
        // define('FREQUENCY_WEEKLY', 1);
        // define('FREQUENCY_TWO_WEEKS', 2);
        // define('FREQUENCY_FOUR_WEEKS', 3);
        // define('FREQUENCY_MONTHLY', 4);
        // define('FREQUENCY_TWO_MONTHS', 5);
        // define('FREQUENCY_THREE_MONTHS', 6);
        // define('FREQUENCY_FOUR_MONTHS', 7);
        // define('FREQUENCY_SIX_MONTHS', 8);
        // define('FREQUENCY_ANNUALLY', 9);
        // define('FREQUENCY_TWO_YEARS', 10);

        return match ($invoice->frequency_id) {
            1       => (int) $end_date->diffInWeeks($start_date),
            2       => (int) $end_date->diffInWeeks($start_date) / 2,
            3       => (int) $end_date->diffInWeeks($start_date) / 4,
            4       => (int) $end_date->diffInMonths($start_date),
            5       => (int) $end_date->diffInMonths($start_date) / 2,
            6       => (int) $end_date->diffInMonths($start_date) / 3,
            7       => (int) $end_date->diffInMonths($start_date) / 4,
            8       => (int) $end_date->diffInMonths($start_date) / 5,
            9       => (int) $end_date->diffInYears($start_date),
            10      => (int) $end_date->diffInYears($start_date) / 2,
            default => -1,
        };
    }

    private function transformDueDate($invoice): string
    {
        //weekly recurring invoice get the payment terms
        if ($invoice->frequency_id == 1) {
            return 'terms';
        }

        $due_date_parts = explode('-', $invoice->due_date);

        if (is_array($due_date_parts) && count($due_date_parts) >= 3) {
            if ($due_date_parts[2] === '00') {
                return '0';
            }

            return (string) $due_date_parts[2];
        }

        return 'terms';
    }

    //v4
    // define('FREQUENCY_WEEKLY', 1);
    // define('FREQUENCY_TWO_WEEKS', 2);
    // define('FREQUENCY_FOUR_WEEKS', 3);
    // define('FREQUENCY_MONTHLY', 4);
    // define('FREQUENCY_TWO_MONTHS', 5);
    // define('FREQUENCY_THREE_MONTHS', 6);
    // define('FREQUENCY_FOUR_MONTHS', 7);
    // define('FREQUENCY_SIX_MONTHS', 8);
    // define('FREQUENCY_ANNUALLY', 9);
    // define('FREQUENCY_TWO_YEARS', 10);

    //v5
    // const FREQUENCY_DAILY = 1;
    // const FREQUENCY_WEEKLY = 2;
    // const FREQUENCY_TWO_WEEKS = 3;
    // const FREQUENCY_FOUR_WEEKS = 4;
    // const FREQUENCY_MONTHLY = 5;
    // const FREQUENCY_TWO_MONTHS = 6;
    // const FREQUENCY_THREE_MONTHS = 7;
    // const FREQUENCY_FOUR_MONTHS = 8;
    // const FREQUENCY_SIX_MONTHS = 9;
    // const FREQUENCY_ANNUALLY = 10;
    // const FREQUENCY_TWO_YEARS = 11;
    // const FREQUENCY_THREE_YEARS = 12;

    private function transformFrequencyId($frequency_id): int
    {
        return match ($frequency_id) {
            1       => 2,
            2       => 3,
            3       => 4,
            4       => 5,
            5       => 6,
            6       => 7,
            7       => 8,
            8       => 9,
            9       => 10,
            10      => 11,
            default => 5,
        };
    }

    /*
        V5
        const STATUS_DRAFT = 1;
        const STATUS_ACTIVE = 2;
        const STATUS_PAUSED = 3;
        const STATUS_COMPLETED = 4;
        const STATUS_PENDING = -1;
     */
    private function transformRecurringStatusId($invoice): int
    {
        if ($invoice->is_deleted == false &&
           $invoice->deleted_at == null &&
           $invoice->is_recurring == true &&
           $invoice->is_public == true &&
           $invoice->frequency_id > 0 &&
           $invoice->start_date <= now() &&
           ($invoice->end_date == null || $invoice->end_date >= now())) {
            return 2;
        }

        if ($invoice->is_public == 0) {
            return 1;
        }

        if ($invoice->end_date && $invoice->end_date < now()) {
            return 4;
        }

        return 1;
    }

    /**
     * const STATUS_DRAFT = 1;
     * const STATUS_SENT = 2;
     * const STATUS_APPROVED = 3;
     * const STATUS_CONVERTED = 4;
     * const STATUS_EXPIRED = -1;.
     */
    private function transformQuoteStatusId($quote): int
    {
        if ($quote->quote_invoice_id) {
            return 4;
        }

        if ( ! $quote->is_public) {
            return 1;
        }

        return match ($quote->invoice_status_id) {
            1       => 1,
            2       => 2,
            3       => 2,
            4       => 3,
            default => 2,
        };
    }

    /*
    define('INVOICE_STATUS_DRAFT', 1);
    define('INVOICE_STATUS_SENT', 2);
    define('INVOICE_STATUS_VIEWED', 3);
    define('INVOICE_STATUS_APPROVED', 4);
    define('INVOICE_STATUS_PARTIAL', 5);
    define('INVOICE_STATUS_PAID', 6);
    define('INVOICE_STATUS_OVERDUE', -1);
    define('INVOICE_STATUS_UNPAID', -2);

    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;
    const STATUS_PARTIAL = 3;
    const STATUS_PAID = 4;
    const STATUS_CANCELLED = 5;
    const STATUS_REVERSED = 6;
     */
    private function transformStatusId($status, $is_public): int
    {
        if ( ! $is_public) {
            return 1;
        }

        return match ($status) {
            1       => 1,
            2       => 2,
            3       => 2,
            4       => 2,
            5       => 3,
            6       => 4,
            default => 2,
        };
    }

    /*
    const STATUS_DRAFT = 1;
    const STATUS_SENT =  2;
    const STATUS_APPROVED = 3;
    const STATUS_EXPIRED = -1;
     */
    private function transformQuoteStatus($status): int
    {
        return match ($status) {
            1       => 1,
            2       => 2,
            4       => 3,
            default => 2,
        };
    }

    /*
    v5
    const CREDIT = 1;
    const ACH = 4;
    const VISA = 5;
    const MASTERCARD = 6;
    const AMERICAN_EXPRESS = 7;
    const DISCOVER = 8;
    const DINERS = 9;
    const EUROCARD = 10;
    const NOVA = 11;
    const CREDIT_CARD_OTHER = 12;
    const PAYPAL = 13;
    const CARTE_BLANCHE = 16;
    const UNIONPAY = 17;
    const JCB = 18;
    const LASER = 19;
    const MAESTRO = 20;
    const SOLO = 21;
    const SWITCH = 22;
    const ALIPAY = 27;
    const SOFORT = 28;
    const SEPA = 29;
    const GOCARDLESS = 30;
    const CRYPTO = 31;

    const MOLLIE_BANK_TRANSFER = 34;
    const KBC = 35;
    const BANCONTACT = 36;
    const IDEAL = 37;
    const HOSTED_PAGE = 38;
    const GIROPAY = 39;
    const PRZELEWY24 = 40;
    const EPS = 41;
    const DIRECT_DEBIT = 42;
    const BECS = 43;
    const ACSS = 44;
    const INSTANT_BANK_PAY = 45;
    const FPX = 46;
    */
    private function transformPaymentType($payment_type_id)
    {
        return match ($payment_type_id) {
            4                              => 42,
            PAYMENT_TYPE_CREDIT            => 32,
            PAYMENT_TYPE_ACH               => 4,
            PAYMENT_TYPE_VISA              => 5,
            PAYMENT_TYPE_MASTERCARD        => 6,
            PAYMENT_TYPE_AMERICAN_EXPRESS  => 7,
            PAYMENT_TYPE_DISCOVER          => 8,
            PAYMENT_TYPE_DINERS            => 9,
            PAYMENT_TYPE_EUROCARD          => 10,
            PAYMENT_TYPE_NOVA              => 11,
            PAYMENT_TYPE_CREDIT_CARD_OTHER => 12,
            PAYMENT_TYPE_PAYPAL            => 13,
            16                             => 15,
            PAYMENT_TYPE_CARTE_BLANCHE     => 16,
            PAYMENT_TYPE_UNIONPAY          => 17,
            PAYMENT_TYPE_JCB               => 18,
            PAYMENT_TYPE_LASER             => 19,
            PAYMENT_TYPE_MAESTRO           => 20,
            PAYMENT_TYPE_SOLO              => 21,
            PAYMENT_TYPE_SWITCH            => 22,
            PAYMENT_TYPE_ALIPAY            => 27,
            PAYMENT_TYPE_SOFORT            => 28,
            PAYMENT_TYPE_SEPA              => 29,
            PAYMENT_TYPE_GOCARDLESS        => 30,
            PAYMENT_TYPE_BITCOIN           => 31,
            2                              => 1,
            3                              => 2,
            default                        => $payment_type_id,
        };
    }

    private function transformPaymentStatus($payment)
    {
        if ($payment->is_deleted && $payment->payment_status_id == 4) {
            return 2;
        }

        return $payment->payment_status_id;
    }

    /**
     * @return array<mixed, array<'amount'|'applied'|'client_id'|'company_id'|'created_at'|'date'|'deleted_at'|'is_deleted'|'refunded'|'status_id'|'updated_at'|'user_id', mixed>>
     */
    private function getCredits(): array
    {
        $credits = Credit::where('account_id', $this->account->id)->where('balance', '>', 0)->whereIsDeleted(false)
            ->withTrashed()
            ->get();

        info('get credits => ' . $credits->count());

        $transformed = [];

        foreach ($credits as $credit) {
            $transformed[] = [
                'client_id'  => $credit->client_id,
                'user_id'    => $credit->user_id,
                'company_id' => $credit->account_id,
                'is_deleted' => $credit->is_deleted,
                'amount'     => $credit->balance ?: 0,
                'applied'    => 0,
                'refunded'   => 0,
                'date'       => $credit->date,
                'created_at' => $credit->created_at ? Carbon::parse($credit->created_at)->toDateString() : null,
                'updated_at' => $credit->updated_at ? Carbon::parse($credit->updated_at)->toDateString() : null,
                'deleted_at' => $credit->deleted_at ? Carbon::parse($credit->deleted_at)->toDateString() : null,
                'status_id'  => 4,
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'company_id'|'created_at'|'disk'|'expense_id'|'hash'|'height'|'id'|'invoice_id'|'name'|'path'|'preview'|'size'|'type'|'updated_at'|'url'|'user_id'|'width', mixed>>
     */
    private function getDocuments(): array
    {
        $documents = Document::where('account_id', $this->account->id)->get();

        info('get documents => ' . $documents->count());

        $transformed = [];

        foreach ($documents as $document) {
            $transformed[] = [
                'id'         => $document->id,
                'user_id'    => $document->user_id,
                'company_id' => $this->account->id,
                'invoice_id' => $document->invoice_id,
                'expense_id' => $document->expense_id,
                'path'       => $document->path,
                'preview'    => $document->preview,
                'name'       => $document->name,
                'type'       => $document->type,
                'disk'       => $document->disk,
                'hash'       => $document->hash,
                'size'       => $document->size,
                'width'      => $document->width,
                'height'     => $document->height,
                'created_at' => $document->created_at ? Carbon::parse($document->created_at)->toDateString() : null,
                'updated_at' => $document->updated_at ? Carbon::parse($document->updated_at)->toDateString() : null,
                'url'        => url('/api/v1/documents/' . $document->public_id),
            ];
        }

        return $transformed;
    }

    private function buildFeesAndLimits($gateway_types): stdClass
    {
        $fees = new stdClass();

        foreach ($gateway_types as $gateway_type) {
            if ($gateway_type == 'token') {
                continue;
            }

            $fees_and_limits = $this->transformFeesAndLimits($gateway_type);

            $translated_gateway_type = $this->translateGatewayTypeId($gateway_type);

            $fees->{$translated_gateway_type} = $fees_and_limits;
        }

        return $fees;
    }

    /**
     * @return array<mixed, array<'accepted_credit_cards'|'config'|'created_at'|'custom_value1'|'custom_value2'|'custom_value3'|'custom_value4'|'deleted_at'|'fees_and_limits'|'gateway_key'|'id'|'require_billing_address'|'require_cvv'|'require_shipping_address'|'update_details'|'updated_at'|'user_id', mixed>>
     */
    private function getCompanyGateways(): array
    {
        $account_gateways = AccountGateway::where('account_id', $this->account->id)->withTrashed()->get();

        info('get get company gateways => ' . $account_gateways->count());

        $transformed = [];

        foreach ($account_gateways as $account_gateway) {
            if ($this->translateGatewaysId($account_gateway->gateway_id) == 0) {
                continue;
            }

            $gateway_types = $account_gateway->paymentDriver()->gatewayTypes();

            $config = 'If you see this message - we were not able to decrypt your config';

            try {
                $config = Crypt::decrypt($account_gateway->config);
            } catch (Exception) {
                // info($config);
            }

            // foreach ($gateway_types as $gateway_type_id) {
            $transformed[] = [
                'id' => $account_gateway->id,
                //'id' => $this->translateGatewaysId($account_gateway->id),
                'user_id'                  => $account_gateway->user_id,
                'gateway_key'              => $this->getGatewayKeyById($this->translateGatewaysId($account_gateway->gateway_id)),
                'accepted_credit_cards'    => $account_gateway->accepted_credit_cards,
                'require_cvv'              => $account_gateway->require_cvv,
                'require_billing_address'  => $account_gateway->show_billing_address,
                'require_shipping_address' => $account_gateway->show_shipping_address,
                'update_details'           => $account_gateway->update_details,
                'config'                   => $config,
                'fees_and_limits'          => $this->buildFeesAndLimits($gateway_types),
                'custom_value1'            => '',
                'custom_value2'            => '',
                'custom_value3'            => '',
                'custom_value4'            => '',
                'created_at'               => $account_gateway->created_at ? Carbon::parse($account_gateway->created_at)->toDateString() : null,
                'updated_at'               => $account_gateway->updated_at ? Carbon::parse($account_gateway->updated_at)->toDateString() : null,
                'deleted_at'               => $account_gateway->deleted_at ? Carbon::parse($account_gateway->deleted_at)->toDateString() : null,
            ];
            // }
        }

        return $transformed;
    }

    // converts the gateway ID to the new v5 list
    private function translateGatewaysId($gateway_id): int
    {
        info('translating gateway ID = ' . $gateway_id);

        return match ($gateway_id) {
            1, 2 => 1,
            3  => 2,
            4  => 3,
            5  => 4,
            6  => 52,
            7  => 5,
            8  => 6,
            9  => 7,
            10 => 8,
            11 => 9,
            12 => 10,
            13 => 11,
            14 => 12,
            15 => 13,
            16 => 14,
            17 => 15,
            18 => 16,
            19 => 17,
            20 => 18,
            21 => 0,
            22 => 19,
            23 => 20,
            24 => 21,
            25 => 22,
            26 => 23,
            27 => 24,
            28 => 25,
            29, 30 => 0,
            31 => 26,
            32 => 27,
            33 => 28,
            34 => 29,
            35 => 30,
            36 => 0,
            37 => 31,
            38 => 0,
            39 => 32,
            40 => 33,
            41 => 34,
            42 => 35,
            43 => 0,
            44 => 36,
            45 => 37,
            46 => 38,
            47 => 39,
            48 => 40,
            49 => 41,
            50 => 42,
            51 => 43,
            52 => 44,
            53, 54 => 0,
            55 => 45,
            56 => 46,
            57 => 47,
            58 => 48,
            59 => 0,
            60 => 49,
            61 => 50,
            62 => 55,
            63 => 51,
            64 => 52,
            65 => 53,
            66 => 54,
            67, 68 => 55,
            default => 0,
        };
    }

    /**
     * @return array<mixed, array<'client_id'|'company_gateway_id'|'company_id'|'created_at'|'deleted_at'|'gateway_customer_reference'|'gateway_type_id'|'id'|'is_default'|'meta'|'token'|'updated_at', mixed>>
     */
    private function getClientGatewayTokens(): array
    {
        $payment_methods = PaymentMethod::where('account_id', $this->account->id)->withTrashed()->get();

        info('get client gateway tokens => ' . $payment_methods->count());

        $transformed = [];

        $is_default = true;

        foreach ($payment_methods as $payment_method) {
            $contact = Contact::where('id', $payment_method->contact_id)->withTrashed()->first();
            $agt = AccountGatewayToken::where('id', $payment_method->account_gateway_token_id)->withTrashed()->first();

            if ( ! $contact && ! $agt) {
                continue;
            }

            $transformed[] = [
                'id'                         => $payment_method->id,
                'company_id'                 => $this->account->id,
                'client_id'                  => $contact->client_id,
                'token'                      => $payment_method->source_reference,
                'company_gateway_id'         => $agt->account_gateway_id,
                'gateway_customer_reference' => $agt->token,
                'gateway_type_id'            => $payment_method->payment_type->gateway_type_id,
                'is_default'                 => $is_default,
                'meta'                       => $this->convertMeta($payment_method),
                'created_at'                 => $payment_method->created_at ? Carbon::parse($payment_method->created_at)->toDateString() : null,
                'updated_at'                 => $payment_method->updated_at ? Carbon::parse($payment_method->updated_at)->toDateString() : null,
                'deleted_at'                 => $payment_method->deleted_at ? Carbon::parse($payment_method->deleted_at)->toDateString() : null,
            ];

            $is_default = false;
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'company_id'|'created_at'|'deleted_at'|'is_deleted'|'num_days'|'updated_at'|'user_id', mixed>>
     */
    private function getPaymentTerms(): array
    {
        $payment_terms = PaymentTerm::where('account_id', 0)->orWhere('account_id', $this->account->id)->withTrashed()->get();

        info('get payment terms => ' . $payment_terms->count());

        $transformed = [];

        foreach ($payment_terms as $payment_term) {
            if ($payment_term->num_days == -1) {
                $payment_term->num_days = 0;
            }

            $transformed[] = [
                'user_id'    => 0,
                'company_id' => $this->account->id,
                'num_days'   => $payment_term->num_days,
                'is_deleted' => $payment_term->is_deleted,
                'created_at' => $payment_term->created_at ? Carbon::parse($payment_term->created_at)->toDateString() : null,
                'updated_at' => $payment_term->updated_at ? Carbon::parse($payment_term->updated_at)->toDateString() : null,
                'deleted_at' => $payment_term->deleted_at ? Carbon::parse($payment_term->deleted_at)->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'company_id'|'created_at'|'deleted_at'|'id'|'is_deleted'|'name'|'status_order'|'updated_at'|'user_id', mixed>>
     */
    private function getTaskStatuses(): array
    {
        $task_statuses = TaskStatus::where('account_id', $this->account->id)->withTrashed()->get();

        info('get task statuses => ' . $task_statuses->count());

        if ($task_statuses->count() == 0) {
            $defaults = [
                'backlog',
                'ready_to_do',
                'in_progress',
                'done',
            ];
            $counter = count($defaults);
            for ($i = 0; $i < $counter; $i++) {
                $status = TaskStatus::createNew();
                $status->name = trans('texts.' . $defaults[$i]);
                $status->sort_order = $i;
                $status->save();
            }

            $task_statuses = TaskStatus::where('account_id', $this->account->id)->withTrashed()->get();
        }

        $transformed = [];

        foreach ($task_statuses as $task_status) {
            $transformed[] = [
                'name'         => $task_status->name ?: '',
                'id'           => $task_status->id,
                'company_id'   => $this->account->id,
                'user_id'      => $task_status->user_id,
                'status_order' => $task_status->sort_order,
                'is_deleted'   => false,
                'created_at'   => $task_status->created_at ? Carbon::parse($task_status->created_at)->toDateString() : null,
                'updated_at'   => $task_status->updated_at ? Carbon::parse($task_status->updated_at)->toDateString() : null,
                'deleted_at'   => $task_status->deleted_at ? Carbon::parse($task_status->deleted_at)->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'company_id'|'created_at'|'deleted_at'|'id'|'is_deleted'|'name'|'updated_at'|'user_id', mixed>>
     */
    private function getExpenseCategories(): array
    {
        $expense_categories = ExpenseCategory::where('account_id', $this->account->id)->withTrashed()->get();

        info('get expense categories => ' . $expense_categories->count());

        $transformed = [];

        foreach ($expense_categories as $category) {
            $transformed[] = [
                'name'       => $category->name ?: '',
                'company_id' => $this->account->id,
                'id'         => $category->id,
                'user_id'    => $category->user_id,
                'is_deleted' => $category->is_deleted,
                'created_at' => $category->created_at ? Carbon::parse($category->created_at)->toDateString() : null,
                'updated_at' => $category->updated_at ? Carbon::parse($category->updated_at)->toDateString() : null,
                'deleted_at' => $category->deleted_at ? Carbon::parse($category->deleted_at)->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'amount'|'bank_id'|'category_id'|'client_id'|'company_id'|'created_at'|'currency_id'|'custom_value1'|'custom_value2'|'custom_value3'|'custom_value4'|'date'|'deleted_at'|'exchange_rate'|'foreign_amount'|'id'|'invoice_currency_id'|'invoice_documents'|'invoice_id'|'is_deleted'|'payment_date'|'payment_type_id'|'private_notes'|'public_notes'|'recurring_expense_id'|'should_be_invoiced'|'tax_name1'|'tax_name2'|'tax_name3'|'tax_rate1'|'tax_rate2'|'tax_rate3'|'transaction_id'|'transaction_reference'|'updated_at'|'user_id'|'vendor_id', mixed>>
     */
    private function getExpenses(): array
    {
        $expenses = Expense::where('account_id', $this->account->id)->withTrashed()->get();

        info('get expenses => ' . $expenses->count());

        $transformed = [];

        foreach ($expenses as $expense) {
            $transformed[] = [
                'id'                    => $expense->id,
                'company_id'            => $this->account->id,
                'user_id'               => $expense->user_id,
                'amount'                => $expense->amount,
                'bank_id'               => $expense->bank_id,
                'client_id'             => $expense->client_id,
                'custom_value1'         => $expense->custom_value1,
                'custom_value2'         => $expense->custom_value2,
                'custom_value3'         => '',
                'custom_value4'         => '',
                'exchange_rate'         => $expense->exchange_rate,
                'category_id'           => $expense->expense_category_id,
                'currency_id'           => $expense->expense_currency_id,
                'date'                  => $expense->expense_date,
                'foreign_amount'        => 0,
                'invoice_currency_id'   => $expense->invoice_currency_id,
                'invoice_documents'     => $expense->invoice_documents,
                'invoice_id'            => $expense->invoice_id,
                'payment_date'          => $expense->payment_date,
                'payment_type_id'       => $this->transformPaymentType($expense->payment_type_id),
                'private_notes'         => $expense->private_notes,
                'public_notes'          => $expense->public_notes,
                'recurring_expense_id'  => $expense->recurring_expense_id,
                'should_be_invoiced'    => $expense->should_be_invoiced,
                'tax_name1'             => $expense->tax_name1,
                'tax_name2'             => $expense->tax_name2,
                'tax_name3'             => '',
                'tax_rate1'             => $expense->tax_rate1,
                'tax_rate2'             => $expense->tax_rate2,
                'tax_rate3'             => 0,
                'transaction_id'        => $expense->transaction_id,
                'transaction_reference' => $expense->transaction_reference,
                'vendor_id'             => $expense->vendor_id,
                'is_deleted'            => $expense->is_deleted,
                'created_at'            => $expense->created_at ? Carbon::parse($expense->created_at)->toDateString() : null,
                'updated_at'            => $expense->updated_at ? Carbon::parse($expense->updated_at)->toDateString() : null,
                'deleted_at'            => $expense->deleted_at ? Carbon::parse($expense->deleted_at)->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'client_id'|'company_id'|'created_at'|'custom_value1'|'custom_value2'|'custom_value3'|'custom_value4'|'deleted_at'|'description'|'id'|'invoice_id'|'is_deleted'|'is_running'|'project_id'|'status_id'|'status_order'|'time_log'|'updated_at'|'user_id', mixed>>
     */
    private function getTasks(): array
    {
        $tasks = Task::where('account_id', $this->account->id)
            ->withTrashed()
            ->get();

        info('get tasks => ' . $tasks->count());

        $transformed = [];

        foreach ($tasks as $task) {
            // if(!($task->deleted_at instanceof Carbon))
            //     $task->deleted_at = Carbon::parse($task->deleted_at);

            $transformed[] = [
                'id'            => $task->id,
                'company_id'    => $this->account->id,
                'client_id'     => $task->client_id,
                'custom_value1' => $task->custom_value1,
                'custom_value2' => $task->custom_value2,
                'custom_value3' => $task->custom_value3,
                'custom_value4' => $task->custom_value4,
                'description'   => $task->description,
                'invoice_id'    => $task->invoice_id,
                'is_running'    => $task->is_running,
                'project_id'    => $task->project_id,
                'status_id'     => $task->task_status_id,
                'status_order'  => $task->task_status_sort_order,
                'time_log'      => $task->time_log,
                'user_id'       => $task->user_id,
                'is_deleted'    => $task->is_deleted,
                'created_at'    => $task->created_at ? Carbon::parse($task->created_at)->toDateString() : null,
                'updated_at'    => $task->updated_at ? Carbon::parse($task->updated_at)->toDateString() : null,
                'deleted_at'    => $task->deleted_at ? Carbon::parse($task->deleted_at)->toDateString() : null,
            ];
        }

        return $transformed;
    }

    /**
     * @return array<mixed, array<'budgeted_hours'|'client_id'|'company_id'|'created_at'|'custom_value1'|'custom_value2'|'custom_value3'|'custom_value4'|'deleted_at'|'due_date'|'id'|'is_deleted'|'name'|'private_notes'|'public_notes'|'task_rate'|'updated_at'|'user_id', mixed>>
     */
    private function getProjects(): array
    {
        $projects = Project::where('account_id', $this->account->id)
            ->withTrashed()
            ->get();

        info('get projects => ' . $projects);

        $transformed = [];

        foreach ($projects as $project) {
            // if(!($project->deleted_at instanceof Carbon))
            //     $project->deleted_at = Carbon::parse($project->deleted_at);

            $transformed[] = [
                'id'             => $project->id,
                'company_id'     => $this->account->id,
                'client_id'      => $project->client_id,
                'custom_value1'  => $project->custom_value1,
                'custom_value2'  => $project->custom_value2,
                'custom_value3'  => $project->custom_value3,
                'custom_value4'  => $project->custom_value4,
                'budgeted_hours' => $project->budgeted_hours,
                'due_date'       => $project->due_date,
                'name'           => $project->name,
                'private_notes'  => $project->private_notes,
                'public_notes'   => '',
                'task_rate'      => $project->task_rate,
                'user_id'        => $project->user_id,
                'is_deleted'     => $project->is_deleted,
                'created_at'     => $project->created_at ? Carbon::parse($project->created_at)->toDateString() : null,
                'updated_at'     => $project->updated_at ? Carbon::parse($project->updated_at)->toDateString() : null,
                'deleted_at'     => $project->deleted_at ? Carbon::parse($project->deleted_at)->toDateString() : null,
            ];
        }

        return $transformed;
    }

    private function convertMeta($payment_method): stdClass
    {
        info('get converting payment method meta');

        $expiry = explode('-', $payment_method->expiration);

        if (is_array($expiry) && count($expiry) >= 2) {
            $exp_month = $expiry[1];
            $exp_year = $expiry[0];
        } else {
            $exp_month = '';
            $exp_year = '';
        }

        $meta = new stdClass();
        $meta->exp_month = (string) $exp_month;
        $meta->exp_year = (string) $exp_year;
        $meta->brand = (string) $payment_method->payment_type->name;
        $meta->last4 = (string) str_replace(',', '', ($payment_method->last4));
        $meta->type = $payment_method->payment_type->gateway_type_id;

        return $meta;
    }

    private function transformFeesAndLimits($gateway_type_id)
    {
        info('get transform fees and limits');

        $ags = AccountGatewaySettings::where('account_id', $this->account->id)
            ->where('gateway_type_id', $gateway_type_id)
            ->first();

        if ( ! $ags) {
            return new stdClass();
        }

        $fees_and_limits = new stdClass();
        $fees_and_limits->min_limit = $ags->min_limit > 0 ? $ags->min_limit : -1;
        $fees_and_limits->max_limit = $ags->max_limit > 0 ? $ags->max_limit : -1;
        $fees_and_limits->fee_amount = $ags->fee_amount;
        $fees_and_limits->fee_percent = $ags->fee_percent;
        $fees_and_limits->fee_tax_name1 = $ags->tax_name1;
        $fees_and_limits->fee_tax_rate1 = $ags->tax_rate1;
        $fees_and_limits->fee_tax_name2 = $ags->tax_name2;
        $fees_and_limits->fee_tax_rate2 = $ags->tax_rate2;
        $fees_and_limits->fee_tax_name3 = '';
        $fees_and_limits->fee_tax_rate3 = 0;
        $fees_and_limits->is_enabled = true;

        return $fees_and_limits;
        // $data = [];
        // $data[1] = $fees_and_limits;
        // return $data;
    }

    /*
    v4
    define('GATEWAY_TYPE_CREDIT_CARD', 1);
    define('GATEWAY_TYPE_BANK_TRANSFER', 2);
    define('GATEWAY_TYPE_PAYPAL', 3);
    define('GATEWAY_TYPE_BITCOIN', 4);
    define('GATEWAY_TYPE_DWOLLA', 5);
    define('GATEWAY_TYPE_CUSTOM1', 6);
    define('GATEWAY_TYPE_ALIPAY', 7);
    define('GATEWAY_TYPE_SOFORT', 8);
    define('GATEWAY_TYPE_SEPA', 9);
    define('GATEWAY_TYPE_GOCARDLESS', 10);
    define('GATEWAY_TYPE_APPLE_PAY', 11);
    define('GATEWAY_TYPE_CUSTOM2', 12);
    define('GATEWAY_TYPE_CUSTOM3', 13);
    define('GATEWAY_TYPE_TOKEN', 'token');

    v5
    const CREDIT_CARD = 1;
    const BANK_TRANSFER = 2;
    const PAYPAL = 3;
    const CRYPTO = 4;
    const CUSTOM = 5;
    const ALIPAY = 6;
    const SOFORT = 7;
    const APPLE_PAY = 8;
    const SEPA = 9;
    const CREDIT = 10;
    */
    private function translateGatewayTypeId($type): int
    {
        return match ($type) {
            1  => 1,
            2  => 2,
            3  => 3,
            4  => 4,
            5  => 2,
            6  => 5,
            7  => 6,
            8  => 7,
            9  => 9,
            10 => 1,
            11 => 8,
            12, 13 => 5,
            default => 1,
        };
    }

    private function getGatewayKeyById($gateway_id)
    {
        $gateways = collect([
            ['id' => 1, 'key' => '3b6621f970ab18887c4f6dca78d3f8bb'],
            ['id' => 2, 'key' => '46c5c1fed2c43acf4f379bae9c8b9f76'],
            ['id' => 3, 'key' => '944c20175bbe6b9972c05bcfe294c2c7'],
            ['id' => 4, 'key' => '4e0ed0d34552e6cb433506d1ac03a418'],
            ['id' => 5, 'key' => '513cdc81444c87c4b07258bc2858d3fa'],
            ['id' => 6, 'key' => '99c2a271b5088951334d1302e038c01a'],
            ['id' => 7, 'key' => '1bd651fb213ca0c9d66ae3c336dc77e8'],
            ['id' => 8, 'key' => 'c3dec814e14cbd7d86abd92ce6789f8c'],
            ['id' => 9, 'key' => '070dffc5ca94f4e66216e44028ebd52d'],
            ['id' => 10, 'key' => '334d419939c06bd99b4dfd8a49243f0f'],
            ['id' => 11, 'key' => 'd6814fc83f45d2935e7777071e629ef9'],
            ['id' => 12, 'key' => '0d97c97d227f91c5d0cb86d01e4a52c9'],
            ['id' => 13, 'key' => 'a66b7062f4c8212d2c428209a34aa6bf'],
            ['id' => 14, 'key' => '7e6fc08b89467518a5953a4839f8baba'],
            ['id' => 15, 'key' => '38f2c48af60c7dd69e04248cbb24c36e'],
            ['id' => 16, 'key' => '80af24a6a69f5c0bbec33e930ab40665'],
            ['id' => 17, 'key' => '0749cb92a6b36c88bd9ff8aabd2efcab'],
            ['id' => 18, 'key' => '4c8f4e5d0f353a122045eb9a60cc0f2d'],
            ['id' => 19, 'key' => '8036a5aadb2bdaafb23502da8790b6a2'],
            ['id' => 20, 'key' => 'd14dd26a37cecc30fdd65700bfb55b23'],
            ['id' => 21, 'key' => 'd14dd26a37cdcc30fdd65700bfb55b23'],
            ['id' => 22, 'key' => 'ea3b328bd72d381387281c3bd83bd97c'],
            ['id' => 23, 'key' => 'a0035fc0d87c4950fb82c73e2fcb825a'],
            ['id' => 24, 'key' => '16dc1d3c8a865425421f64463faaf768'],
            ['id' => 25, 'key' => '43e639234f660d581ddac725ba7bcd29'],
            ['id' => 26, 'key' => '2f71dc17b0158ac30a7ae0839799e888'],
            ['id' => 27, 'key' => '733998ee4760b10f11fb48652571e02c'],
            ['id' => 28, 'key' => '6312879223e49c5cf92e194646bdee8f'],
            ['id' => 29, 'key' => '106ef7e7da9062b0df363903b455711c'],
            ['id' => 30, 'key' => 'e9a38f0896b5b82d196be3b7020c8664'],
            ['id' => 31, 'key' => '0da4e18ed44a5bd5c8ec354d0ab7b301'],
            ['id' => 32, 'key' => 'd3979e62eb603fbdf1c78fe3a8ba7009'],
            ['id' => 33, 'key' => '557d98977e7ec02dfa53de4b69b335be'],
            ['id' => 34, 'key' => '54dc60c869a7322d87efbec5c0c25805'],
            ['id' => 35, 'key' => 'e4a02f0a4b235eb5e9e294730703bb74'],
            ['id' => 36, 'key' => '1b3c6f3ccfea4f5e7eadeae188cccd7f'],
            ['id' => 37, 'key' => '7cba6ce5c125f9cb47ea8443ae671b68'],
            ['id' => 38, 'key' => 'b98cfa5f750e16cee3524b7b7e78fbf6'],
            ['id' => 39, 'key' => '3758e7f7c6f4cecf0f4f348b9a00f456'],
            ['id' => 40, 'key' => 'cbc7ef7c99d31ec05492fbcb37208263'],
            ['id' => 41, 'key' => 'e186a98d3b079028a73390bdc11bdb82'],
            ['id' => 42, 'key' => '761040aca40f685d1ab55e2084b30670'],
            ['id' => 43, 'key' => '1b2cef0e8c800204a29f33953aaf3360'],
            ['id' => 44, 'key' => '7ea2d40ecb1eb69ef8c3d03e5019028a'],
            ['id' => 45, 'key' => '70ab90cd6c5c1ab13208b3cef51c0894'],
            ['id' => 46, 'key' => 'bbd736b3254b0aabed6ad7fda1298c88'],
            ['id' => 47, 'key' => '231cb401487b9f15babe04b1ac4f7a27'],
            ['id' => 48, 'key' => 'bad8699d581d9fa040e59c0bb721a76c'],
            ['id' => 49, 'key' => '8fdeed552015b3c7b44ed6c8ebd9e992'],
            ['id' => 50, 'key' => 'f7ec488676d310683fb51802d076d713'],
            ['id' => 51, 'key' => '30334a52fb698046572c627ca10412e8'],
            ['id' => 52, 'key' => 'b9886f9257f0c6ee7c302f1c74475f6c'],
            ['id' => 53, 'key' => 'ef498756b54db63c143af0ec433da803'],
            ['id' => 54, 'key' => 'ca52f618a39367a4c944098ebf977e1c'],
            ['id' => 55, 'key' => '54faab2ab6e3223dbe848b1686490baa'],
        ]);

        $search = $gateways->where('id', $gateway_id)->pluck('key');

        return $search[0];
    }
}
