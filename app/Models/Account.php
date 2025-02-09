<?php

namespace App\Models;

use App\Events\UserSettingsChanged;
use App\Libraries\Utils;
use App\Models\Traits\GeneratesNumbers;
use App\Models\Traits\HasCustomMessages;
use App\Models\Traits\HasLogo;
use App\Models\Traits\PresentsInvoice;
use App\Models\Traits\SendsEmails;
use App\Ninja\Presenters\AccountPresenter;
use Carbon;
use DateTime;
use DateTimeZone;
use Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Account.
 *
 * @property int                                     $id
 * @property int|null                                $timezone_id
 * @property int|null                                $date_format_id
 * @property int|null                                $datetime_format_id
 * @property int|null                                $currency_id
 * @property \Illuminate\Support\Carbon|null         $created_at
 * @property \Illuminate\Support\Carbon|null         $updated_at
 * @property \Illuminate\Support\Carbon|null         $deleted_at
 * @property string|null                             $name
 * @property string                                  $ip
 * @property string                                  $account_key
 * @property string|null                             $last_login
 * @property string|null                             $address1
 * @property string|null                             $address2
 * @property string|null                             $city
 * @property string|null                             $state
 * @property string|null                             $postal_code
 * @property int|null                                $country_id
 * @property string|null                             $invoice_terms
 * @property string|null                             $email_footer
 * @property int|null                                $industry_id
 * @property int|null                                $size_id
 * @property int                                     $invoice_taxes
 * @property int                                     $invoice_item_taxes
 * @property int                                     $invoice_design_id
 * @property string|null                             $work_phone
 * @property string|null                             $work_email
 * @property int                                     $language_id
 * @property string|null                             $custom_value1
 * @property string|null                             $custom_value2
 * @property int                                     $fill_products
 * @property int                                     $update_products
 * @property string|null                             $primary_color
 * @property string|null                             $secondary_color
 * @property int                                     $hide_quantity
 * @property int                                     $hide_paid_to_date
 * @property int|null                                $custom_invoice_taxes1
 * @property int|null                                $custom_invoice_taxes2
 * @property string|null                             $vat_number
 * @property string|null                             $invoice_number_prefix
 * @property int|null                                $invoice_number_counter
 * @property string|null                             $quote_number_prefix
 * @property int|null                                $quote_number_counter
 * @property int                                     $share_counter
 * @property string|null                             $id_number
 * @property int                                     $token_billing_type_id
 * @property string|null                             $invoice_footer
 * @property int                                     $pdf_email_attachment
 * @property string|null                             $subdomain
 * @property int                                     $font_size
 * @property string|null                             $invoice_labels
 * @property string|null                             $custom_design1
 * @property int                                     $show_item_taxes
 * @property string|null                             $iframe_url
 * @property int                                     $military_time
 * @property int                                     $enable_reminder1
 * @property int                                     $enable_reminder2
 * @property int                                     $enable_reminder3
 * @property int                                     $num_days_reminder1
 * @property int                                     $num_days_reminder2
 * @property int                                     $num_days_reminder3
 * @property int                                     $recurring_hour
 * @property string|null                             $invoice_number_pattern
 * @property string|null                             $quote_number_pattern
 * @property string|null                             $quote_terms
 * @property int                                     $email_design_id
 * @property int                                     $enable_email_markup
 * @property string|null                             $website
 * @property int                                     $direction_reminder1
 * @property int                                     $direction_reminder2
 * @property int                                     $direction_reminder3
 * @property int                                     $field_reminder1
 * @property int                                     $field_reminder2
 * @property int                                     $field_reminder3
 * @property string|null                             $client_view_css
 * @property int                                     $header_font_id
 * @property int                                     $body_font_id
 * @property int                                     $auto_convert_quote
 * @property int                                     $all_pages_footer
 * @property int                                     $all_pages_header
 * @property int                                     $show_currency_code
 * @property int                                     $enable_portal_password
 * @property int                                     $send_portal_password
 * @property string                                  $recurring_invoice_number_prefix
 * @property int                                     $enable_client_portal
 * @property string|null                             $invoice_fields
 * @property string|null                             $devices
 * @property string|null                             $logo
 * @property int                                     $logo_width
 * @property int                                     $logo_height
 * @property int                                     $logo_size
 * @property int                                     $invoice_embed_documents
 * @property int                                     $document_email_attachment
 * @property int                                     $enable_client_portal_dashboard
 * @property int|null                                $company_id
 * @property string                                  $page_size
 * @property int                                     $live_preview
 * @property int                                     $invoice_number_padding
 * @property int                                     $enable_second_tax_rate
 * @property int                                     $auto_bill_on_due_date
 * @property int                                     $start_of_week
 * @property int                                     $enable_buy_now_buttons
 * @property int                                     $include_item_taxes_inline
 * @property string|null                             $financial_year_start
 * @property int                                     $enabled_modules
 * @property int                                     $enabled_dashboard_sections
 * @property int                                     $show_accept_invoice_terms
 * @property int                                     $show_accept_quote_terms
 * @property int                                     $require_invoice_signature
 * @property int                                     $require_quote_signature
 * @property string|null                             $client_number_prefix
 * @property int|null                                $client_number_counter
 * @property string|null                             $client_number_pattern
 * @property int|null                                $domain_id
 * @property int|null                                $payment_terms
 * @property int|null                                $reset_counter_frequency_id
 * @property int|null                                $payment_type_id
 * @property int                                     $gateway_fee_enabled
 * @property string|null                             $reset_counter_date
 * @property string|null                             $tax_name1
 * @property string                                  $tax_rate1
 * @property string|null                             $tax_name2
 * @property string                                  $tax_rate2
 * @property int                                     $quote_design_id
 * @property string|null                             $custom_design2
 * @property string|null                             $custom_design3
 * @property string|null                             $analytics_key
 * @property int|null                                $credit_number_counter
 * @property string|null                             $credit_number_prefix
 * @property string|null                             $credit_number_pattern
 * @property string                                  $task_rate
 * @property int                                     $inclusive_taxes
 * @property int                                     $convert_products
 * @property int                                     $enable_reminder4
 * @property int                                     $signature_on_pdf
 * @property int                                     $ubl_email_attachment
 * @property int|null                                $auto_archive_invoice
 * @property int|null                                $auto_archive_quote
 * @property int|null                                $auto_email_invoice
 * @property int|null                                $send_item_details
 * @property mixed|null                              $custom_fields
 * @property int|null                                $background_image_id
 * @property mixed|null                              $custom_messages
 * @property int                                     $is_custom_domain
 * @property int                                     $show_product_notes
 * @property AccountEmailSettings|null               $account_email_settings
 * @property Collection<int, AccountGatewaySettings> $account_gateway_settings
 * @property int|null                                $account_gateway_settings_count
 * @property Collection<int, AccountGateway>         $account_gateways
 * @property int|null                                $account_gateways_count
 * @property Collection<int, AccountToken>           $account_tokens
 * @property int|null                                $account_tokens_count
 * @property Document|null                           $background_image
 * @property Collection<int, BankAccount>            $bank_accounts
 * @property int|null                                $bank_accounts_count
 * @property Collection<int, Client>                 $clients
 * @property int|null                                $clients_count
 * @property Company|null                            $company
 * @property Collection<int, Contact>                $contacts
 * @property int|null                                $contacts_count
 * @property Country|null                            $country
 * @property Currency|null                           $currency
 * @property Collection<int, PaymentTerm>            $custom_payment_terms
 * @property int|null                                $custom_payment_terms_count
 * @property DateFormat|null                         $date_format
 * @property DatetimeFormat|null                     $datetime_format
 * @property Collection<int, Document>               $defaultDocuments
 * @property int|null                                $default_documents_count
 * @property Collection<int, ExpenseCategory>        $expense_categories
 * @property int|null                                $expense_categories_count
 * @property Collection<int, Expense>                $expenses
 * @property int|null                                $expenses_count
 * @property Industry|null                           $industry
 * @property Collection<int, Invoice>                $invoices
 * @property int|null                                $invoices_count
 * @property Language                                $language
 * @property PaymentType|null                        $payment_type
 * @property Collection<int, Payment>                $payments
 * @property int|null                                $payments_count
 * @property Collection<int, Product>                $products
 * @property int|null                                $products_count
 * @property Collection<int, Project>                $projects
 * @property int|null                                $projects_count
 * @property Size|null                               $size
 * @property Collection<int, TaskStatus>             $task_statuses
 * @property int|null                                $task_statuses_count
 * @property Collection<int, TaxRate>                $tax_rates
 * @property int|null                                $tax_rates_count
 * @property Timezone|null                           $timezone
 * @property Collection<int, User>                   $users
 * @property int|null                                $users_count
 *
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account onlyTrashed()
 * @method static Builder|Account query()
 * @method static Builder|Account whereAccountKey($value)
 * @method static Builder|Account whereAddress1($value)
 * @method static Builder|Account whereAddress2($value)
 * @method static Builder|Account whereAllPagesFooter($value)
 * @method static Builder|Account whereAllPagesHeader($value)
 * @method static Builder|Account whereAnalyticsKey($value)
 * @method static Builder|Account whereAutoArchiveInvoice($value)
 * @method static Builder|Account whereAutoArchiveQuote($value)
 * @method static Builder|Account whereAutoBillOnDueDate($value)
 * @method static Builder|Account whereAutoConvertQuote($value)
 * @method static Builder|Account whereAutoEmailInvoice($value)
 * @method static Builder|Account whereBackgroundImageId($value)
 * @method static Builder|Account whereBodyFontId($value)
 * @method static Builder|Account whereCity($value)
 * @method static Builder|Account whereClientNumberCounter($value)
 * @method static Builder|Account whereClientNumberPattern($value)
 * @method static Builder|Account whereClientNumberPrefix($value)
 * @method static Builder|Account whereClientViewCss($value)
 * @method static Builder|Account whereCompanyId($value)
 * @method static Builder|Account whereConvertProducts($value)
 * @method static Builder|Account whereCountryId($value)
 * @method static Builder|Account whereCreatedAt($value)
 * @method static Builder|Account whereCreditNumberCounter($value)
 * @method static Builder|Account whereCreditNumberPattern($value)
 * @method static Builder|Account whereCreditNumberPrefix($value)
 * @method static Builder|Account whereCurrencyId($value)
 * @method static Builder|Account whereCustomDesign1($value)
 * @method static Builder|Account whereCustomDesign2($value)
 * @method static Builder|Account whereCustomDesign3($value)
 * @method static Builder|Account whereCustomFields($value)
 * @method static Builder|Account whereCustomInvoiceTaxes1($value)
 * @method static Builder|Account whereCustomInvoiceTaxes2($value)
 * @method static Builder|Account whereCustomMessages($value)
 * @method static Builder|Account whereCustomValue1($value)
 * @method static Builder|Account whereCustomValue2($value)
 * @method static Builder|Account whereDateFormatId($value)
 * @method static Builder|Account whereDatetimeFormatId($value)
 * @method static Builder|Account whereDeletedAt($value)
 * @method static Builder|Account whereDevices($value)
 * @method static Builder|Account whereDirectionReminder1($value)
 * @method static Builder|Account whereDirectionReminder2($value)
 * @method static Builder|Account whereDirectionReminder3($value)
 * @method static Builder|Account whereDocumentEmailAttachment($value)
 * @method static Builder|Account whereDomainId($value)
 * @method static Builder|Account whereEmailDesignId($value)
 * @method static Builder|Account whereEmailFooter($value)
 * @method static Builder|Account whereEnableBuyNowButtons($value)
 * @method static Builder|Account whereEnableClientPortal($value)
 * @method static Builder|Account whereEnableClientPortalDashboard($value)
 * @method static Builder|Account whereEnableEmailMarkup($value)
 * @method static Builder|Account whereEnablePortalPassword($value)
 * @method static Builder|Account whereEnableReminder1($value)
 * @method static Builder|Account whereEnableReminder2($value)
 * @method static Builder|Account whereEnableReminder3($value)
 * @method static Builder|Account whereEnableReminder4($value)
 * @method static Builder|Account whereEnableSecondTaxRate($value)
 * @method static Builder|Account whereEnabledDashboardSections($value)
 * @method static Builder|Account whereEnabledModules($value)
 * @method static Builder|Account whereFieldReminder1($value)
 * @method static Builder|Account whereFieldReminder2($value)
 * @method static Builder|Account whereFieldReminder3($value)
 * @method static Builder|Account whereFillProducts($value)
 * @method static Builder|Account whereFinancialYearStart($value)
 * @method static Builder|Account whereFontSize($value)
 * @method static Builder|Account whereGatewayFeeEnabled($value)
 * @method static Builder|Account whereHeaderFontId($value)
 * @method static Builder|Account whereHidePaidToDate($value)
 * @method static Builder|Account whereHideQuantity($value)
 * @method static Builder|Account whereId($value)
 * @method static Builder|Account whereIdNumber($value)
 * @method static Builder|Account whereIframeUrl($value)
 * @method static Builder|Account whereIncludeItemTaxesInline($value)
 * @method static Builder|Account whereInclusiveTaxes($value)
 * @method static Builder|Account whereIndustryId($value)
 * @method static Builder|Account whereInvoiceDesignId($value)
 * @method static Builder|Account whereInvoiceEmbedDocuments($value)
 * @method static Builder|Account whereInvoiceFields($value)
 * @method static Builder|Account whereInvoiceFooter($value)
 * @method static Builder|Account whereInvoiceItemTaxes($value)
 * @method static Builder|Account whereInvoiceLabels($value)
 * @method static Builder|Account whereInvoiceNumberCounter($value)
 * @method static Builder|Account whereInvoiceNumberPadding($value)
 * @method static Builder|Account whereInvoiceNumberPattern($value)
 * @method static Builder|Account whereInvoiceNumberPrefix($value)
 * @method static Builder|Account whereInvoiceTaxes($value)
 * @method static Builder|Account whereInvoiceTerms($value)
 * @method static Builder|Account whereIp($value)
 * @method static Builder|Account whereIsCustomDomain($value)
 * @method static Builder|Account whereLanguageId($value)
 * @method static Builder|Account whereLastLogin($value)
 * @method static Builder|Account whereLivePreview($value)
 * @method static Builder|Account whereLogo($value)
 * @method static Builder|Account whereLogoHeight($value)
 * @method static Builder|Account whereLogoSize($value)
 * @method static Builder|Account whereLogoWidth($value)
 * @method static Builder|Account whereMilitaryTime($value)
 * @method static Builder|Account whereName($value)
 * @method static Builder|Account whereNumDaysReminder1($value)
 * @method static Builder|Account whereNumDaysReminder2($value)
 * @method static Builder|Account whereNumDaysReminder3($value)
 * @method static Builder|Account wherePageSize($value)
 * @method static Builder|Account wherePaymentTerms($value)
 * @method static Builder|Account wherePaymentTypeId($value)
 * @method static Builder|Account wherePdfEmailAttachment($value)
 * @method static Builder|Account wherePostalCode($value)
 * @method static Builder|Account wherePrimaryColor($value)
 * @method static Builder|Account whereQuoteDesignId($value)
 * @method static Builder|Account whereQuoteNumberCounter($value)
 * @method static Builder|Account whereQuoteNumberPattern($value)
 * @method static Builder|Account whereQuoteNumberPrefix($value)
 * @method static Builder|Account whereQuoteTerms($value)
 * @method static Builder|Account whereRecurringHour($value)
 * @method static Builder|Account whereRecurringInvoiceNumberPrefix($value)
 * @method static Builder|Account whereRequireInvoiceSignature($value)
 * @method static Builder|Account whereRequireQuoteSignature($value)
 * @method static Builder|Account whereResetCounterDate($value)
 * @method static Builder|Account whereResetCounterFrequencyId($value)
 * @method static Builder|Account whereSecondaryColor($value)
 * @method static Builder|Account whereSendItemDetails($value)
 * @method static Builder|Account whereSendPortalPassword($value)
 * @method static Builder|Account whereShareCounter($value)
 * @method static Builder|Account whereShowAcceptInvoiceTerms($value)
 * @method static Builder|Account whereShowAcceptQuoteTerms($value)
 * @method static Builder|Account whereShowCurrencyCode($value)
 * @method static Builder|Account whereShowItemTaxes($value)
 * @method static Builder|Account whereShowProductNotes($value)
 * @method static Builder|Account whereSignatureOnPdf($value)
 * @method static Builder|Account whereSizeId($value)
 * @method static Builder|Account whereStartOfWeek($value)
 * @method static Builder|Account whereState($value)
 * @method static Builder|Account whereSubdomain($value)
 * @method static Builder|Account whereTaskRate($value)
 * @method static Builder|Account whereTaxName1($value)
 * @method static Builder|Account whereTaxName2($value)
 * @method static Builder|Account whereTaxRate1($value)
 * @method static Builder|Account whereTaxRate2($value)
 * @method static Builder|Account whereTimezoneId($value)
 * @method static Builder|Account whereTokenBillingTypeId($value)
 * @method static Builder|Account whereUblEmailAttachment($value)
 * @method static Builder|Account whereUpdateProducts($value)
 * @method static Builder|Account whereUpdatedAt($value)
 * @method static Builder|Account whereVatNumber($value)
 * @method static Builder|Account whereWebsite($value)
 * @method static Builder|Account whereWorkEmail($value)
 * @method static Builder|Account whereWorkPhone($value)
 * @method static Builder|Account withTrashed()
 * @method static Builder|Account withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Account extends Model
{
    use GeneratesNumbers;
    use HasCustomMessages;
    use HasLogo;
    use PresentableTrait;
    use PresentsInvoice;
    use SendsEmails;
    use SoftDeletes;

    /**
     * @var array
     */
    public static $basicSettings = [
        ACCOUNT_COMPANY_DETAILS,
        ACCOUNT_USER_DETAILS,
        ACCOUNT_LOCALIZATION,
        ACCOUNT_PAYMENTS,
        ACCOUNT_TAX_RATES,
        ACCOUNT_PRODUCTS,
        ACCOUNT_NOTIFICATIONS,
        ACCOUNT_IMPORT_EXPORT,
        ACCOUNT_MANAGEMENT,
    ];

    /**
     * @var array
     */
    public static $advancedSettings = [
        ACCOUNT_INVOICE_SETTINGS,
        ACCOUNT_INVOICE_DESIGN,
        ACCOUNT_CLIENT_PORTAL,
        ACCOUNT_EMAIL_SETTINGS,
        ACCOUNT_TEMPLATES_AND_REMINDERS,
        ACCOUNT_BANKS,
        //ACCOUNT_REPORTS,
        ACCOUNT_DATA_VISUALIZATIONS,
        ACCOUNT_API_TOKENS,
        ACCOUNT_USER_MANAGEMENT,
    ];

    public static $modules = [
        ENTITY_RECURRING_INVOICE => 1,
        ENTITY_CREDIT            => 2,
        ENTITY_QUOTE             => 4,
        ENTITY_TASK              => 8,
        ENTITY_EXPENSE           => 16,
    ];

    public static $dashboardSections = [
        'total_revenue'   => 1,
        'average_invoice' => 2,
        'outstanding'     => 4,
    ];

    public static $customFields = [
        'client1',
        'client2',
        'contact1',
        'contact2',
        'product1',
        'product2',
        'invoice1',
        'invoice2',
        'invoice_surcharge1',
        'invoice_surcharge2',
        'task1',
        'task2',
        'project1',
        'project2',
        'expense1',
        'expense2',
        'vendor1',
        'vendor2',
    ];

    public static $customLabels = [
        'address1',
        'address2',
        'amount',
        'amount_paid',
        'balance',
        'balance_due',
        'blank',
        'city_state_postal',
        'client_name',
        'company_name',
        'contact_name',
        'country',
        'credit_card',
        'credit_date',
        'credit_issued_to',
        'credit_note',
        'credit_number',
        'credit_to',
        'custom_value1',
        'custom_value2',
        'date',
        'delivery_note',
        'description',
        'details',
        'discount',
        'due_date',
        'email',
        'from',
        'gateway_fee_description',
        'gateway_fee_discount_description',
        'gateway_fee_item',
        'hours',
        'id_number',
        'invoice',
        'invoice_date',
        'invoice_due_date',
        'invoice_issued_to',
        'invoice_no',
        'invoice_number',
        'invoice_to',
        'invoice_total',
        'item',
        'line_total',
        'method',
        'outstanding',
        'paid_to_date',
        'partial_due',
        'payment_date',
        'phone',
        'po_number',
        'postal_city_state',
        'product_key',
        'quantity',
        'quote',
        'quote_date',
        'quote_due_date',
        'quote_issued_to',
        'quote_no',
        'quote_number',
        'quote_to',
        'rate',
        'reference',
        'service',
        'statement',
        'statement_date',
        'statement_issued_to',
        'statement_to',
        'subtotal',
        'surcharge',
        'tax',
        'tax_invoice',
        'tax_quote',
        'taxes',
        'terms',
        'to',
        'total',
        'unit_cost',
        'valid_until',
        'vat_number',
        'website',
        'work_phone',
        'your_credit',
        'your_invoice',
        'your_quote',
        'your_statement',
    ];

    public static $customMessageTypes = [
        CUSTOM_MESSAGE_DASHBOARD,
        CUSTOM_MESSAGE_UNPAID_INVOICE,
        CUSTOM_MESSAGE_PAID_INVOICE,
        CUSTOM_MESSAGE_UNAPPROVED_QUOTE,
        //CUSTOM_MESSAGE_APPROVED_QUOTE,
        //CUSTOM_MESSAGE_UNAPPROVED_PROPOSAL,
        //CUSTOM_MESSAGE_APPROVED_PROPOSAL,
    ];

    /**
     * @var string
     */
    protected $presenter = AccountPresenter::class;

    /**
     * @var array
     */
    protected $hidden = ['ip'];

    /**
     * @var array
     */
    protected $fillable = [
        'timezone_id',
        'date_format_id',
        'datetime_format_id',
        'currency_id',
        'name',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'invoice_terms',
        'email_footer',
        'industry_id',
        'size_id',
        'invoice_taxes',
        'invoice_item_taxes',
        'invoice_design_id',
        'quote_design_id',
        'work_phone',
        'work_email',
        'language_id',
        'fill_products',
        'update_products',
        'primary_color',
        'secondary_color',
        'hide_quantity',
        'hide_paid_to_date',
        'vat_number',
        'invoice_number_prefix',
        'invoice_number_counter',
        'quote_number_prefix',
        'quote_number_counter',
        'share_counter',
        'id_number',
        'token_billing_type_id',
        'invoice_footer',
        'pdf_email_attachment',
        'font_size',
        'invoice_labels',
        'custom_design1',
        'custom_design2',
        'custom_design3',
        'show_item_taxes',
        'military_time',
        'enable_reminder1',
        'enable_reminder2',
        'enable_reminder3',
        'enable_reminder4',
        'num_days_reminder1',
        'num_days_reminder2',
        'num_days_reminder3',
        'tax_name1',
        'tax_rate1',
        'tax_name2',
        'tax_rate2',
        'recurring_hour',
        'invoice_number_pattern',
        'quote_number_pattern',
        'quote_terms',
        'email_design_id',
        'enable_email_markup',
        'website',
        'direction_reminder1',
        'direction_reminder2',
        'direction_reminder3',
        'field_reminder1',
        'field_reminder2',
        'field_reminder3',
        'header_font_id',
        'body_font_id',
        'auto_convert_quote',
        'auto_archive_quote',
        'auto_archive_invoice',
        'auto_email_invoice',
        'all_pages_footer',
        'all_pages_header',
        'show_currency_code',
        'enable_portal_password',
        'send_portal_password',
        'recurring_invoice_number_prefix',
        'enable_client_portal',
        'invoice_fields',
        'invoice_embed_documents',
        'document_email_attachment',
        'ubl_email_attachment',
        'enable_client_portal_dashboard',
        'page_size',
        'live_preview',
        'invoice_number_padding',
        'enable_second_tax_rate',
        'auto_bill_on_due_date',
        'start_of_week',
        'enable_buy_now_buttons',
        'include_item_taxes_inline',
        'financial_year_start',
        'enabled_modules',
        'enabled_dashboard_sections',
        'show_accept_invoice_terms',
        'show_accept_quote_terms',
        'require_invoice_signature',
        'require_quote_signature',
        'client_number_prefix',
        'client_number_counter',
        'client_number_pattern',
        'payment_terms',
        'reset_counter_frequency_id',
        'payment_type_id',
        'gateway_fee_enabled',
        'send_item_details',
        'reset_counter_date',
        'domain_id',
        'analytics_key',
        'credit_number_counter',
        'credit_number_prefix',
        'credit_number_pattern',
        'task_rate',
        'inclusive_taxes',
        'convert_products',
        'signature_on_pdf',
        'custom_fields',
        'custom_value1',
        'custom_value2',
        'custom_messages',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    public function account_tokens()
    {
        return $this->hasMany(AccountToken::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function account_gateways()
    {
        return $this->hasMany(AccountGateway::class);
    }

    public function account_gateway_settings()
    {
        return $this->hasMany(AccountGatewaySettings::class);
    }

    public function account_email_settings()
    {
        return $this->hasOne(AccountEmailSettings::class);
    }

    public function bank_accounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function tax_rates()
    {
        return $this->hasMany(TaxRate::class);
    }

    public function task_statuses()
    {
        return $this->hasMany(TaskStatus::class)->orderBy('sort_order');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function defaultDocuments()
    {
        return $this->hasMany(Document::class)->whereIsDefault(true);
    }

    public function background_image()
    {
        return $this->hasOne(Document::class, 'id', 'background_image_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function date_format()
    {
        return $this->belongsTo(DateFormat::class);
    }

    public function datetime_format()
    {
        return $this->belongsTo(DatetimeFormat::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'account_id', 'id')->withTrashed();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'account_id', 'id')->withTrashed();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function expense_categories()
    {
        return $this->hasMany(ExpenseCategory::class, 'account_id', 'id')->withTrashed();
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'account_id', 'id')->withTrashed();
    }

    public function custom_payment_terms()
    {
        return $this->hasMany(PaymentTerm::class, 'account_id', 'id')->withTrashed();
    }

    /**
     * @param $value
     */
    public function setIndustryIdAttribute($value): void
    {
        $this->attributes['industry_id'] = $value ?: null;
    }

    /**
     * @param $value
     */
    public function setCountryIdAttribute($value): void
    {
        $this->attributes['country_id'] = $value ?: null;
    }

    /**
     * @param $value
     */
    public function setSizeIdAttribute($value): void
    {
        $this->attributes['size_id'] = $value ?: null;
    }

    /**
     * @param $value
     */
    public function setCustomFieldsAttribute($data): void
    {
        $fields = [];

        if ( ! is_array($data)) {
            $data = json_decode($data);
        }

        foreach ($data as $key => $value) {
            if ($value) {
                $fields[$key] = $value;
            }
        }

        $this->attributes['custom_fields'] = count($fields) ? json_encode($fields) : null;
    }

    public function getCustomFieldsAttribute($value): mixed
    {
        return json_decode($value ?: '{}');
    }

    public function customLabel($field)
    {
        $labels = $this->custom_fields;

        return empty($labels->{$field}) ? '' : $labels->{$field};
    }

    /**
     * @param int $gatewayId
     *
     * @return bool
     */
    public function isGatewayConfigured($gatewayId = 0): bool
    {
        if ( ! $this->relationLoaded('account_gateways')) {
            $this->load('account_gateways');
        }

        if ($gatewayId) {
            return $this->getGatewayConfig($gatewayId) != false;
        }

        return $this->account_gateways->count() > 0;
    }

    public function isEnglish(): bool
    {
        return ! $this->language_id || $this->language_id == DEFAULT_LANGUAGE;
    }

    public function hasInvoicePrefix()
    {
        if ( ! $this->invoice_number_prefix && ! $this->quote_number_prefix) {
            return false;
        }

        return $this->invoice_number_prefix != $this->quote_number_prefix;
    }

    public function getDisplayName()
    {
        if ($this->name) {
            return $this->name;
        }

        //$this->load('users');
        $user = $this->users()->first();

        return $user->getDisplayName();
    }

    public function getGatewaySettings($gatewayTypeId)
    {
        if ( ! $this->relationLoaded('account_gateway_settings')) {
            $this->load('account_gateway_settings');
        }

        foreach ($this->account_gateway_settings as $settings) {
            if ($settings->gateway_type_id == $gatewayTypeId) {
                return $settings;
            }
        }

        return false;
    }

    public function getCityState()
    {
        $swap = $this->country && $this->country->swap_postal_code;

        return Utils::cityStateZip($this->city, $this->state, $this->postal_code, $swap);
    }

    public function getMomentDateTimeFormat()
    {
        $format = $this->datetime_format ? $this->datetime_format->format_moment : DEFAULT_DATETIME_MOMENT_FORMAT;

        if ($this->military_time) {
            return str_replace('h:mm:ss a', 'H:mm:ss', $format);
        }

        return $format;
    }

    public function getMomentDateFormat(): string
    {
        $format = $this->getMomentDateTimeFormat();
        $format = str_replace('h:mm:ss a', '', $format);
        $format = str_replace('H:mm:ss', '', $format);

        return trim($format);
    }

    public function getTimezone()
    {
        if ($this->timezone) {
            return $this->timezone->name;
        }

        return 'US/Eastern';
    }

    public function getDate($date = 'now')
    {
        if ( ! $date) {
            return;
        }

        if ( ! $date instanceof DateTime) {
            return new DateTime($date);
        }

        return $date;
    }

    /**
     * @param string $date
     *
     * @return DateTime|null|string
     */
    public function getDateTime($date = 'now', $formatted = false)
    {
        $date = $this->getDate($date);
        $date->setTimeZone(new DateTimeZone($this->getTimezone()));

        return $formatted ? $date->format($this->getCustomDateTimeFormat()) : $date;
    }

    public function getCustomDateFormat()
    {
        return $this->date_format ? $this->date_format->format : DEFAULT_DATE_FORMAT;
    }

    public function getSampleLink()
    {
        $invitation = new Invitation();
        $invitation->account = $this;
        $invitation->invitation_key = '...';

        return $invitation->getLink();
    }

    /**
     * @param       $amount
     * @param null  $client
     * @param bool  $hideSymbol
     * @param mixed $decorator
     *
     * @return string
     */
    public function formatMoney($amount, $client = null, $decorator = false)
    {
        if ($client && $client->currency_id) {
            $currencyId = $client->currency_id;
        } elseif ($this->currency_id) {
            $currencyId = $this->currency_id;
        } else {
            $currencyId = DEFAULT_CURRENCY;
        }

        if ($client && $client->country_id) {
            $countryId = $client->country_id;
        } elseif ($this->country_id) {
            $countryId = $this->country_id;
        } else {
            $countryId = false;
        }

        if ( ! $decorator) {
            $decorator = $this->show_currency_code ? CURRENCY_DECORATOR_CODE : CURRENCY_DECORATOR_SYMBOL;
        }

        return Utils::formatMoney($amount, $currencyId, $countryId, $decorator);
    }

    public function formatNumber($amount, $precision = 0)
    {
        $currencyId = $this->currency_id ? $this->currency_id : DEFAULT_CURRENCY;

        return Utils::formatNumber($amount, $currencyId, $precision);
    }

    public function getCurrencyId()
    {
        return $this->currency_id ?: DEFAULT_CURRENCY;
    }

    public function getCountryId()
    {
        return $this->country_id ?: DEFAULT_COUNTRY;
    }

    /**
     * @param $date
     *
     * @return null|string
     */
    public function formatDate($date)
    {
        $date = $this->getDate($date);

        if ( ! $date) {
            return;
        }

        return $date->format($this->getCustomDateFormat());
    }

    /**
     * @param $date
     *
     * @return null|string
     */
    public function formatDateTime($date)
    {
        $date = $this->getDateTime($date);

        if ( ! $date) {
            return;
        }

        return $date->format($this->getCustomDateTimeFormat());
    }

    /**
     * @param $date
     *
     * @return null|string
     */
    public function formatTime($date)
    {
        $date = $this->getDateTime($date);

        if ( ! $date) {
            return;
        }

        return $date->format($this->getCustomTimeFormat());
    }

    public function getCustomTimeFormat(): string
    {
        return $this->military_time ? 'H:i' : 'g:i a';
    }

    public function getCustomDateTimeFormat()
    {
        $format = $this->datetime_format ? $this->datetime_format->format : DEFAULT_DATETIME_FORMAT;

        if ($this->military_time) {
            return str_replace('g:i a', 'H:i', $format);
        }

        return $format;
    }

    /*
    public function defaultGatewayType()
    {
        $accountGateway = $this->account_gateways[0];
        $paymentDriver = $accountGateway->paymentDriver();

        return $paymentDriver->gatewayTypes()[0];
    }
    */

    /**
     * @param bool $type
     *
     * @return AccountGateway|bool
     */
    public function getGatewayByType($type = false)
    {
        if ( ! $this->relationLoaded('account_gateways')) {
            $this->load('account_gateways');
        }

        /** @var AccountGateway $accountGateway */
        foreach ($this->account_gateways as $accountGateway) {
            if ( ! $type) {
                return $accountGateway;
            }

            $paymentDriver = $accountGateway->paymentDriver();

            if ($paymentDriver->handles($type)) {
                return $accountGateway;
            }
        }

        return false;
    }

    public function availableGatewaysIds(): array
    {
        if ( ! $this->relationLoaded('account_gateways')) {
            $this->load('account_gateways');
        }

        $gatewayTypes = [];
        $gatewayIds = [];
        $usedGatewayIds = [];

        foreach ($this->account_gateways as $accountGateway) {
            $usedGatewayIds[] = $accountGateway->gateway_id;
            $paymentDriver = $accountGateway->paymentDriver();
            $gatewayTypes = array_unique(array_merge($gatewayTypes, $paymentDriver->gatewayTypes()));
        }

        foreach (Cache::get('gateways') as $gateway) {
            $paymentDriverClass = AccountGateway::paymentDriverClass($gateway->provider);
            $paymentDriver = new $paymentDriverClass();
            $available = true;

            foreach ($gatewayTypes as $type) {
                if ($paymentDriver->handles($type)) {
                    $available = false;
                    break;
                }
            }

            if ($available) {
                $gatewayIds[] = $gateway->id;
            }
        }

        return $gatewayIds;
    }

    /**
     * @param bool  $invitation
     * @param mixed $gatewayTypeId
     *
     * @return bool
     */
    public function paymentDriver($invitation = false, $gatewayTypeId = false)
    {
        /** @var AccountGateway $accountGateway */
        if ($accountGateway = $this->getGatewayByType($gatewayTypeId)) {
            return $accountGateway->paymentDriver($invitation, $gatewayTypeId);
        }

        return false;
    }

    public function gatewayIds()
    {
        return $this->account_gateways()->pluck('gateway_id')->toArray();
    }

    /**
     * @param $gatewayId
     *
     * @return bool
     */
    public function hasGatewayId($gatewayId): bool
    {
        return in_array($gatewayId, $this->gatewayIds());
    }

    /**
     * @param $gatewayId
     *
     * @return bool
     */
    public function getGatewayConfig($gatewayId)
    {
        foreach ($this->account_gateways as $gateway) {
            if ($gateway->gateway_id == $gatewayId) {
                return $gateway;
            }
        }

        return false;
    }

    public function getPrimaryUser()
    {
        return $this->users()
            ->orderBy('id')
            ->first();
    }

    /**
     * @param $userId
     * @param $name
     *
     * @return null
     */
    public function getToken($userId, $name)
    {
        foreach ($this->account_tokens as $token) {
            if ($token->user_id == $userId && $token->name === $name) {
                return $token->token;
            }
        }
    }

    /**
     * @param      $entityType
     * @param null $clientId
     *
     * @return mixed
     */
    public function createInvoice($entityType = ENTITY_INVOICE, $clientId = null)
    {
        $invoice = Invoice::createNew();

        $invoice->is_recurring = false;
        $invoice->invoice_type_id = INVOICE_TYPE_STANDARD;
        $invoice->invoice_date = Utils::today();
        $invoice->start_date = Utils::today();
        $invoice->invoice_design_id = $this->invoice_design_id;
        $invoice->client_id = $clientId;
        $invoice->custom_taxes1 = $this->custom_invoice_taxes1;
        $invoice->custom_taxes2 = $this->custom_invoice_taxes2;

        if ($entityType === ENTITY_RECURRING_INVOICE) {
            $invoice->invoice_number = microtime(true);
            $invoice->is_recurring = true;
        } else {
            if ($entityType == ENTITY_QUOTE) {
                $invoice->invoice_type_id = INVOICE_TYPE_QUOTE;
                $invoice->invoice_design_id = $this->quote_design_id;
            }

            if ($this->hasClientNumberPattern($invoice) && ! $clientId) {
                // do nothing, we don't yet know the value
            } elseif ( ! $invoice->invoice_number) {
                $invoice->invoice_number = $this->getNextNumber($invoice);
            }
        }

        if ( ! $clientId) {
            $invoice->client = Client::createNew();
            $invoice->client->public_id = 0;
        }

        return $invoice;
    }

    /**
     * @param bool $client
     */
    public function loadLocalizationSettings($client = false): void
    {
        $this->load('timezone', 'date_format', 'datetime_format', 'language');

        $timezone = $this->timezone ? $this->timezone->name : DEFAULT_TIMEZONE;
        Session::put(SESSION_TIMEZONE, $timezone);

        Session::put(SESSION_DATE_FORMAT, $this->date_format ? $this->date_format->format : DEFAULT_DATE_FORMAT);
        Session::put(SESSION_DATE_PICKER_FORMAT, $this->date_format ? $this->date_format->picker_format : DEFAULT_DATE_PICKER_FORMAT);

        //php 7.3
        // $currencyId = ($client && $client->currency_id) ? $client->currency_id : $this->currency_id ?: DEFAULT_CURRENCY;
        //php 7.4
        $currencyId = ($client && $client->currency_id) ? $client->currency_id : ($this->currency_id ?: DEFAULT_CURRENCY);

        // $currencyId = ($client && $client->currency_id) ? $client->currency_id : $this->currency_id ?: DEFAULT_CURRENCY;

        $locale = ($client && $client->language_id) ? $client->language->locale : ($this->language_id ? $this->Language->locale : DEFAULT_LOCALE);

        Session::put(SESSION_CURRENCY, $currencyId);
        Session::put(SESSION_CURRENCY_DECORATOR, $this->show_currency_code ? CURRENCY_DECORATOR_CODE : CURRENCY_DECORATOR_SYMBOL);
        Session::put(SESSION_LOCALE, $locale);

        App::setLocale($locale);

        $format = $this->datetime_format ? $this->datetime_format->format : DEFAULT_DATETIME_FORMAT;
        if ($this->military_time) {
            $format = str_replace('g:i a', 'H:i', $format);
        }

        Session::put(SESSION_DATETIME_FORMAT, $format);

        Session::put('start_of_week', $this->start_of_week);
    }

    public function isNinjaAccount(): bool
    {
        return str_starts_with($this->account_key, 'zg4ylmzDkdkPOT8yoKQw9LTWaoZJx7');
    }

    public function isNinjaOrLicenseAccount()
    {
        if ($this->isNinjaAccount()) {
            return true;
        }

        return $this->account_key == NINJA_LICENSE_ACCOUNT_KEY;
    }

    /**
     * @param $plan
     */
    public function startTrial($plan): void
    {
        if ( ! Utils::isNinja()) {
            return;
        }

        if ($this->company->trial_started && $this->company->trial_started != '0000-00-00') {
            return;
        }

        $this->company->trial_plan = $plan;
        $this->company->trial_started = date_create()->format('Y-m-d');
        $this->company->save();
    }

    public function hasReminders()
    {
        if ( ! $this->hasFeature(FEATURE_EMAIL_TEMPLATES_REMINDERS)) {
            return false;
        }

        return $this->enable_reminder1 || $this->enable_reminder2 || $this->enable_reminder3 || $this->enable_reminder4;
    }

    /**
     * @param $feature
     *
     * @return bool
     */
    public function hasFeature($feature)
    {
        if (Utils::isNinjaDev()) {
            return true;
        }

        $planDetails = $this->getPlanDetails();
        $selfHost = ! Utils::isNinjaProd();

        if ( ! $selfHost && function_exists('ninja_account_features')) {
            $result = ninja_account_features($this, $feature);

            if ($result != null) {
                return $result;
            }
        }

        switch ($feature) {
            // Pro
            case FEATURE_TASKS:
            case FEATURE_EXPENSES:
            case FEATURE_QUOTES:
                return true;

            case FEATURE_CUSTOMIZE_INVOICE_DESIGN:
            case FEATURE_DIFFERENT_DESIGNS:
            case FEATURE_EMAIL_TEMPLATES_REMINDERS:
            case FEATURE_INVOICE_SETTINGS:
            case FEATURE_CUSTOM_EMAILS:
            case FEATURE_PDF_ATTACHMENT:
            case FEATURE_MORE_INVOICE_DESIGNS:
            case FEATURE_REPORTS:
            case FEATURE_BUY_NOW_BUTTONS:
            case FEATURE_API:
            case FEATURE_CLIENT_PORTAL_PASSWORD:
            case FEATURE_CUSTOM_URL:
                return $selfHost || ! empty($planDetails);

                // Pro; No trial allowed, unless they're trialing enterprise with an active pro plan
            case FEATURE_MORE_CLIENTS:
                return $selfHost || ! empty($planDetails) && ( ! $planDetails['trial'] || ! empty($this->getPlanDetails(false, false)));

                // White Label
            case FEATURE_WHITE_LABEL:
                if ($this->isNinjaAccount() || ( ! $selfHost && $planDetails && ! $planDetails['expires'])) {
                    return false;
                }
                // Fallthrough
                // no break
            case FEATURE_REMOVE_CREATED_BY:
                return ! empty($planDetails); // A plan is required even for self-hosted users

                // Enterprise; No Trial allowed; grandfathered for old pro users
            case FEATURE_USERS:// Grandfathered for old Pro users
                if ($planDetails && $planDetails['trial']) {
                    // Do they have a non-trial plan?
                    $planDetails = $this->getPlanDetails(false, false);
                }

                return $selfHost || ! empty($planDetails) && ($planDetails['plan'] == PLAN_ENTERPRISE || $planDetails['started'] <= date_create(PRO_USERS_GRANDFATHER_DEADLINE));

                // Enterprise; No Trial allowed
            case FEATURE_DOCUMENTS:
            case FEATURE_USER_PERMISSIONS:
                return $selfHost || ! empty($planDetails) && $planDetails['plan'] == PLAN_ENTERPRISE && ! $planDetails['trial'];

            default:
                return false;
        }
    }

    public function isPaid()
    {
        return Utils::isNinja() ? ($this->isPro() && ! $this->isTrial()) : Utils::isWhiteLabel();
    }

    /**
     * @param null $plan_details
     *
     * @return bool
     */
    public function isPro(&$plan_details = null)
    {
        if ( ! Utils::isNinjaProd()) {
            return true;
        }

        if ($this->isNinjaAccount()) {
            return true;
        }

        $plan_details = $this->getPlanDetails();

        return ! empty($plan_details);
    }

    public function hasActivePromo()
    {
        return $this->company->hasActivePromo();
    }

    /**
     * @param null $plan_details
     *
     * @return bool
     */
    public function isEnterprise(&$plan_details = null)
    {
        if ( ! Utils::isNinjaProd()) {
            return true;
        }

        if ($this->isNinjaAccount()) {
            return true;
        }

        $plan_details = $this->getPlanDetails();

        return $plan_details && $plan_details['plan'] == PLAN_ENTERPRISE;
    }

    /**
     * @param bool $include_inactive
     * @param bool $include_trial
     *
     * @return array|null
     */
    public function getPlanDetails($include_inactive = false, $include_trial = true)
    {
        if ( ! $this->company) {
            return;
        }

        $plan = $this->company->plan;
        $price = $this->company->plan_price;
        $trial_plan = $this->company->trial_plan;

        if (( ! $plan || $plan == PLAN_FREE) && ( ! $trial_plan || ! $include_trial)) {
            return;
        }

        $trial_active = false;
        if ($trial_plan && $include_trial) {
            $trial_started = DateTime::createFromFormat('Y-m-d', $this->company->trial_started);
            $trial_expires = clone $trial_started;
            $trial_expires->modify('+2 weeks');

            if ($trial_expires >= date_create()) {
                $trial_active = true;
            }
        }

        $plan_active = false;
        if ($plan) {
            if ($this->company->plan_expires == null) {
                $plan_active = true;
                $plan_expires = false;
            } else {
                $plan_expires = DateTime::createFromFormat('Y-m-d', $this->company->plan_expires);
                if ($plan_expires >= date_create()) {
                    $plan_active = true;
                }
            }
        }

        if ( ! $include_inactive && ! $plan_active && ! $trial_active) {
            return;
        }

        // Should we show plan details or trial details?
        if (($plan && ! $trial_plan) || ! $include_trial) {
            $use_plan = true;
        } elseif ( ! $plan && $trial_plan) {
            $use_plan = false;
        } elseif ($plan_active && $trial_active === false) {
            // There is both a plan and a trial
            $use_plan = true;
        } elseif ($plan_active === false && $trial_active) {
            $use_plan = false;
        } elseif ($plan_active && $trial_active) {
            // Both are active; use whichever is a better plan
            if ($plan == PLAN_ENTERPRISE) {
                $use_plan = true;
            } elseif ($trial_plan == PLAN_ENTERPRISE) {
                $use_plan = false;
            } else {
                // They're both the same; show the plan
                $use_plan = true;
            }
        } else {
            // Neither are active; use whichever expired most recently
            $use_plan = $plan_expires >= $trial_expires;
        }

        if ($use_plan) {
            return [
                'company_id' => $this->company->id,
                'num_users'  => $this->company->num_users,
                'plan_price' => $price,
                'trial'      => false,
                'plan'       => $plan,
                'started'    => DateTime::createFromFormat('Y-m-d', $this->company->plan_started),
                'expires'    => $plan_expires,
                'paid'       => DateTime::createFromFormat('Y-m-d', $this->company->plan_paid),
                'term'       => $this->company->plan_term,
                'active'     => $plan_active,
            ];
        }

        return [
            'company_id' => $this->company->id,
            'num_users'  => 1,
            'plan_price' => 0,
            'trial'      => true,
            'plan'       => $trial_plan,
            'started'    => $trial_started,
            'expires'    => $trial_expires,
            'active'     => $trial_active,
        ];
    }

    public function isTrial()
    {
        if ( ! Utils::isNinjaProd()) {
            return false;
        }

        $plan_details = $this->getPlanDetails();

        return $plan_details && $plan_details['trial'];
    }

    public function getCountTrialDaysLeft()
    {
        $planDetails = $this->getPlanDetails(true);

        if ( ! $planDetails || ! $planDetails['trial']) {
            return 0;
        }

        $today = new DateTime('now');
        $interval = $today->diff($planDetails['expires']);

        return $interval ? $interval->d : 0;
    }

    public function getRenewalDate()
    {
        $planDetails = $this->getPlanDetails();

        if ($planDetails) {
            $date = $planDetails['expires'];
            $date = max($date, date_create());
        } else {
            $date = date_create();
        }

        return Carbon::instance($date);
    }

    /**
     * @param $eventId
     *
     * @return Model|null|static
     */
    public function getSubscriptions($eventId)
    {
        return Subscription::where('account_id', '=', $this->id)->where('event_id', '=', $eventId)->get();
    }

    /**
     * @return $this
     */
    public function hideFieldsForViz(): static
    {
        foreach ($this->clients as $client) {
            $client->setVisible([
                'public_id',
                'name',
                'balance',
                'paid_to_date',
                'invoices',
                'contacts',
                'currency_id',
                'currency',
            ]);

            foreach ($client->invoices as $invoice) {
                $invoice->setVisible([
                    'public_id',
                    'invoice_number',
                    'amount',
                    'balance',
                    'invoice_status_id',
                    'invoice_items',
                    'created_at',
                    'is_recurring',
                    'invoice_type_id',
                    'is_public',
                    'due_date',
                ]);

                foreach ($invoice->invoice_items as $invoiceItem) {
                    $invoiceItem->setVisible([
                        'product_key',
                        'cost',
                        'qty',
                        'discount',
                    ]);
                }
            }

            foreach ($client->contacts as $contact) {
                $contact->setVisible([
                    'public_id',
                    'first_name',
                    'last_name',
                    'email', ]);
            }
        }

        return $this;
    }

    /**
     * @param null $storage_gateway
     *
     * @return bool
     */
    public function showTokenCheckbox(&$storage_gateway = null)
    {
        if ( ! ($storage_gateway = $this->getTokenGatewayId())) {
            return false;
        }

        return $this->token_billing_type_id == TOKEN_BILLING_OPT_IN
                || $this->token_billing_type_id == TOKEN_BILLING_OPT_OUT;
    }

    public function getTokenGatewayId(): int|bool
    {
        if ($this->isGatewayConfigured(GATEWAY_STRIPE)) {
            return GATEWAY_STRIPE;
        }

        if ($this->isGatewayConfigured(GATEWAY_BRAINTREE)) {
            return GATEWAY_BRAINTREE;
        }

        if ($this->isGatewayConfigured(GATEWAY_WEPAY)) {
            return GATEWAY_WEPAY;
        }

        return false;
    }

    /**
     * @return bool|null
     */
    public function getTokenGateway()
    {
        $gatewayId = $this->getTokenGatewayId();
        if ( ! $gatewayId) {
            return;
        }

        return $this->getGatewayConfig($gatewayId);
    }

    public function getLocale()
    {
        return $this->language_id && $this->language ? $this->language->locale : DEFAULT_LOCALE;
    }

    public function selectTokenCheckbox(): bool
    {
        return $this->token_billing_type_id == TOKEN_BILLING_OPT_OUT;
    }

    public function getSiteUrl()
    {
        $url = trim(SITE_URL, '/');
        $iframe_url = $this->iframe_url;

        if ($iframe_url) {
            return $iframe_url . '/?';
        }

        if ($this->subdomain) {
            return Utils::replaceSubdomain($url, $this->subdomain);
        }

        return $url;
    }

    /**
     * @param $host
     *
     * @return bool
     */
    public function checkSubdomain($host)
    {
        if ( ! $this->subdomain) {
            return true;
        }

        $server = explode('.', $host);
        $subdomain = $server[0];

        return ! ( ! in_array($subdomain, ['app', 'www']) && $subdomain != $this->subdomain);
    }

    public function attachPDF(): bool
    {
        return $this->hasFeature(FEATURE_PDF_ATTACHMENT) && $this->pdf_email_attachment;
    }

    public function attachUBL(): bool
    {
        return $this->hasFeature(FEATURE_PDF_ATTACHMENT) && $this->ubl_email_attachment;
    }

    public function getEmailDesignId()
    {
        return $this->hasFeature(FEATURE_CUSTOM_EMAILS) ? $this->email_design_id : EMAIL_DESIGN_PLAIN;
    }

    public function clientViewCSS(): string
    {
        $css = '';

        if ($this->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN)) {
            $bodyFont = $this->getBodyFontCss();
            $headerFont = $this->getHeaderFontCss();

            $css = 'body{' . $bodyFont . '}';
            if ($headerFont !== $bodyFont) {
                $css .= 'h1,h2,h3,h4,h5,h6,.h1,.h2,.h3,.h4,.h5,.h6{' . $headerFont . '}';
            }

            $css .= $this->client_view_css;
        }

        return $css;
    }

    /**
     * @param string $protocol
     *
     * @return string
     */
    public function getFontsUrl(?string $protocol = ''): string
    {
        $bodyFont = $this->getHeaderFontId();
        $headerFont = $this->getBodyFontId();

        $bodyFontSettings = Utils::getFromCache($bodyFont, 'fonts');
        $google_fonts = [$bodyFontSettings['google_font']];

        if ($headerFont != $bodyFont) {
            $headerFontSettings = Utils::getFromCache($headerFont, 'fonts');
            $google_fonts[] = $headerFontSettings['google_font'];
        }

        return ($protocol ? $protocol . ':' : '') . '//fonts.googleapis.com/css?family=' . implode('|', $google_fonts);
    }

    public function getHeaderFontId()
    {
        return ($this->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) && $this->header_font_id) ? $this->header_font_id : DEFAULT_HEADER_FONT;
    }

    public function getBodyFontId()
    {
        return ($this->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) && $this->body_font_id) ? $this->body_font_id : DEFAULT_BODY_FONT;
    }

    /**
     * @return null
     */
    public function getHeaderFontName()
    {
        return Utils::getFromCache($this->getHeaderFontId(), 'fonts')['name'];
    }

    /**
     * @return null
     */
    public function getBodyFontName()
    {
        return Utils::getFromCache($this->getBodyFontId(), 'fonts')['name'];
    }

    /**
     * @param bool $include_weight
     *
     * @return string
     */
    public function getHeaderFontCss($include_weight = true): string
    {
        $font_data = Utils::getFromCache($this->getHeaderFontId(), 'fonts');
        $css = 'font-family:' . $font_data['css_stack'] . ';';

        if ($include_weight) {
            $css .= 'font-weight:' . $font_data['css_weight'] . ';';
        }

        return $css;
    }

    /**
     * @param bool $include_weight
     *
     * @return string
     */
    public function getBodyFontCss($include_weight = true): string
    {
        $font_data = Utils::getFromCache($this->getBodyFontId(), 'fonts');
        $css = 'font-family:' . $font_data['css_stack'] . ';';

        if ($include_weight) {
            $css .= 'font-weight:' . $font_data['css_weight'] . ';';
        }

        return $css;
    }

    public function getFonts(): array
    {
        return array_unique([$this->getHeaderFontId(), $this->getBodyFontId()]);
    }

    public function getFontsData(): array
    {
        $data = [];

        foreach ($this->getFonts() as $font) {
            $data[] = Utils::getFromCache($font, 'fonts');
        }

        return $data;
    }

    public function getFontFolders(): array
    {
        return array_map(fn ($item) => $item['folder'], $this->getFontsData());
    }

    public function isModuleEnabled($entityType): bool|int
    {
        if ( ! in_array($entityType, [
            ENTITY_RECURRING_INVOICE,
            ENTITY_CREDIT,
            ENTITY_QUOTE,
            ENTITY_TASK,
            ENTITY_EXPENSE,
            ENTITY_VENDOR,
            ENTITY_PROJECT,
            ENTITY_PROPOSAL,
        ])) {
            return true;
        }

        if ($entityType == ENTITY_VENDOR) {
            $entityType = ENTITY_EXPENSE;
        } elseif ($entityType == ENTITY_PROJECT) {
            $entityType = ENTITY_TASK;
        } elseif ($entityType == ENTITY_PROPOSAL) {
            $entityType = ENTITY_QUOTE;
        }

        // note: single & checks bitmask match
        return $this->enabled_modules & static::$modules[$entityType];
    }

    public function requiresAuthorization($invoice)
    {
        if ($this->showAcceptTerms($invoice)) {
            return true;
        }

        return (bool) $this->showSignature($invoice);
    }

    public function showAcceptTerms($invoice)
    {
        if ( ! $this->isPro()) {
            return false;
        }

        return $invoice->isQuote() ? $this->show_accept_quote_terms : $this->show_accept_invoice_terms;
    }

    public function showSignature($invoice)
    {
        if ( ! $this->isPro()) {
            return false;
        }

        return $invoice->isQuote() ? $this->require_quote_signature : $this->require_invoice_signature;
    }

    public function emailMarkupEnabled()
    {
        if ( ! Utils::isNinja()) {
            return false;
        }

        return $this->enable_email_markup;
    }

    public function defaultDaysDue($client = false)
    {
        if ($client && $client->payment_terms != 0) {
            return $client->defaultDaysDue();
        }

        return $this->payment_terms == -1 ? 0 : $this->payment_terms;
    }

    public function defaultDueDate($client = false)
    {
        if ($client && $client->payment_terms != 0) {
            $numDays = $client->defaultDaysDue();
        } elseif ($this->payment_terms != 0) {
            $numDays = $this->defaultDaysDue();
        } else {
            return;
        }

        return Carbon::now()->addDays($numDays)->format('Y-m-d');
    }

    public function hasMultipleAccounts(): bool
    {
        return $this->company->accounts->count() > 1;
    }

    public function hasMultipleUsers(): bool
    {
        return $this->users->count() > 1;
    }

    public function getPrimaryAccount()
    {
        return $this->company->accounts()->orderBy('id')->first();
    }

    public function financialYearStartMonth()
    {
        if ( ! $this->financial_year_start) {
            return 1;
        }

        $yearStart = Carbon::parse($this->financial_year_start);

        return $yearStart ? $yearStart->month : 1;
    }

    public function financialYearStart()
    {
        if ( ! $this->financial_year_start) {
            return false;
        }

        $yearStart = Carbon::parse($this->financial_year_start);
        $yearStart->year = date('Y');

        if ($yearStart->isFuture()) {
            $yearStart->subYear();
        }

        return $yearStart->format('Y-m-d');
    }

    public function isClientPortalPasswordEnabled(): bool
    {
        return $this->hasFeature(FEATURE_CLIENT_PORTAL_PASSWORD) && $this->enable_portal_password;
    }

    public function getBaseUrl()
    {
        if ($this->hasFeature(FEATURE_CUSTOM_URL)) {
            if ($this->iframe_url) {
                return $this->iframe_url;
            }

            $url = Utils::isNinjaProd() && ! Utils::isReseller() ? $this->present()->clientPortalLink() : url('/');

            if ($this->subdomain) {
                return Utils::replaceSubdomain($url, $this->subdomain);
            }

            return $url;
        }

        return url('/');
    }

    public function requiresAddressState(): bool
    {
        return true;
        //return ! $this->country_id || $this->country_id == DEFAULT_COUNTRY;
    }
}

Account::creating(function ($account): void {
    LookupAccount::createAccount($account->account_key, $account->company_id);
});

Account::updating(function ($account): void {
    $dirty = $account->getDirty();
    if (array_key_exists('subdomain', $dirty)) {
        LookupAccount::updateAccount($account->account_key, $account);
    }
});

Account::updated(function ($account): void {
    // prevent firing event if the invoice/quote counter was changed
    // TODO: remove once counters are moved to separate table
    $dirty = $account->getDirty();
    if (isset($dirty['invoice_number_counter']) || isset($dirty['quote_number_counter'])) {
        return;
    }

    \Illuminate\Support\Facades\Event::dispatch(new UserSettingsChanged());
});

Account::deleted(function ($account): void {
    LookupAccount::deleteWhere([
        'account_key' => $account->account_key,
    ]);
});
