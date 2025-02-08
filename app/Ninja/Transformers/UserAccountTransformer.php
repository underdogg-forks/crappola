<?php

namespace App\Ninja\Transformers;

use App\Models\Company;
use App\Models\User;
use League\Fractal\Resource\Collection;

class UserAccountTransformer extends EntityTransformer
{
    /**
     * @SWG\Property(property="account_key", type="string", example="123456")
     * @SWG\Property(property="name", type="string", example="John Doe")
     * @SWG\Property(property="token", type="string", example="Token")
     * @SWG\Property(property="default_url", type="string", example="http://www.example.com")
     * @SWG\Property(property="plan", type="string", example="Plan")
     * @SWG\Property(property="logo", type="string", example="Logo")
     * @SWG\Property(property="logo_url", type="string", example="http://www.example.com/logo.png")
     * @SWG\Property(property="currency_id", type="integer", example=1)
     * @SWG\Property(property="timezone_id", type="integer", example=1)
     * @SWG\Property(property="date_format_id", type="integer", example=1)
     * @SWG\Property(property="datetime_format_id", type="integer", example=1)
     * @SWG\Property(property="invoice_terms", type="string", example="Terms")
     * @SWG\Property(property="invoice_taxes", type="boolean", example=false)
     * @SWG\Property(property="invoice_item_taxes", type="boolean", example=false)
     * @SWG\Property(property="invoice_design_id", type="integer", example=1)
     * @SWG\Property(property="quote_design_id", type="integer", example=1)
     * @SWG\Property(property="language_id", type="integer", example=1)
     * @SWG\Property(property="country_id", type="integer", example=1)
     * @SWG\Property(property="invoice_footer", type="string", example="Footer")
     * @SWG\Property(property="invoice_labels", type="string", example="Labels")
     * @SWG\Property(property="show_item_taxes", type="boolean", example=false)
     * @SWG\Property(property="military_time", type="boolean", example=false)
     * @SWG\Property(property="fill_products", type="boolean", example=false)
     * @SWG\Property(property="tax_name1", type="string", example="VAT")
     * @SWG\Property(property="tax_name2", type="string", example="Upkeep")
     * @SWG\Property(property="tax_rate1", type="number", format="float", example="17.5")
     * @SWG\Property(property="tax_rate2", type="number", format="float", example="30.0")
     * @SWG\Property(property="quote_terms", type="string", example="Labels")
     * @SWG\Property(property="show_currency_code", type="boolean", example=false)
     * @SWG\Property(property="enable_second_tax_rate", type="boolean", example=false)
     * @SWG\Property(property="start_of_week", type="string", example="Monday")
     * @SWG\Property(property="financial_year_start", type="string", example="January")
     * @SWG\Property(property="enabled_modules", type="integer", example=1)
     * @SWG\Property(property="payment_terms", type="integer", example=1)
     * @SWG\Property(property="payment_type_id", type="integer", example=1)
     * @SWG\Property(property="task_rate", type="number", format="float", example="17.5")
     * @SWG\Property(property="inclusive_taxes", type="boolean", example=false)
     * @SWG\Property(property="convert_products", type="boolean", example=false)
     * @SWG\Property(property="custom_invoice_taxes1", type="string", example="Value")
     * @SWG\Property(property="custom_invoice_taxes2", type="string", example="Value")
     * @SWG\Property(property="custom_fields", type="string", example="Value")
     */
    protected array $defaultIncludes = [
        'user',
    ];

    /**
     * @var array
     */
    protected array $availableIncludes = [
		'users',
        'tax_rates',
        'expense_categories',
        'account_email_settings',
        'custom_payment_terms',
        'task_statuses',
    ];

    protected $tokenName;

    public function __construct(company $company, $serializer, $tokenName)
    {
        parent::__construct($company, $serializer);

        $this->tokenName = $tokenName;
    }

    public function includeUser(User $user)
    {
        $transformer = new UserTransformer($this->company, $this->serializer);

        return $this->includeItem($user, $transformer, 'user');
    }

    /**
     * @param company $company
     *
     * @return Collection
     */
    public function includeCustomPaymentTerms(User $user)
    {
        $transformer = new PaymentTermTransformer($this->company, $this->serializer);

        return $this->includeCollection($this->company->custom_payment_terms, $transformer, 'payment_terms');
    }

    /**
     * @param company $company
     *
     * @return Collection
     */
    public function includeUsers(User $user)
    {
        $transformer = new UserTransformer($this->company, $this->serializer);

        return $this->includeCollection($this->company->users, $transformer, 'users');
    }

    /**
     * @param company $company
     *
     * @return Collection
     */
    public function includeTaskStatuses(User $user)
    {
        $transformer = new TaskStatusTransformer($this->company, $this->serializer);

        return $this->includeCollection($this->company->task_statuses, $transformer, 'task_statuses');
    }

    /**
     * @param company $company
     *
     * @return Collection
     */
    public function includeAccountEmailSettings(User $user)
    {
        $transformer = new AccountEmailSettingsTransformer($this->company, $this->serializer);

        return $this->includeItem($this->company->account_email_settings, $transformer, 'account_email_settings');
    }

    /**
     * @param company $company
     *
     * @return Collection
     */
    public function includeExpenseCategories(User $user)
    {
        $transformer = new ExpenseCategoryTransformer($this->company, $this->serializer);

        return $this->includeCollection($this->company->expense_categories, $transformer, 'expense_categories');
    }

    /**
     * @param company $company
     *
     * @return Collection
     */
    public function includeTaxRates(User $user)
    {
        $transformer = new TaxRateTransformer($this->company, $this->serializer);

        return $this->includeCollection($this->company->tax_rates, $transformer, 'tax_rates');
    }

    public function transform(User $user)
    {
        $company = $user->company;
        $companyPlan = $company->companyPlan;

        return [
            'account_key' => $company->account_key,
            'user_id' => (int)($user->public_id + 1),
            'name' => $company->present()->name ?: '',
            'token' => $company->getToken($user->id, $this->tokenName),
            'default_url' => SITE_URL,
            'plan' => $companyPlan->hasActivePlan() && $companyPlan->plan ? $companyPlan->plan : '',
            'logo' => $company->logo ?: '',
            'logo_url' => $company->getLogoURL() ?: '',
            'currency_id' => (int)$company->currency_id,
            'timezone_id' => (int)$company->timezone_id,
            'date_format_id' => (int)$company->date_format_id,
            'datetime_format_id' => (int)$company->datetime_format_id,
            'invoice_terms' => $company->invoice_terms ?: '',
            'invoice_taxes' => (bool)$company->invoice_taxes,
            'invoice_item_taxes' => (bool)$company->invoice_item_taxes,
            'invoice_design_id' => (int)$company->invoice_design_id,
            'quote_design_id' => (int)$company->quote_design_id,
            'language_id' => (int)$company->language_id,
            'country_id' => (int)$company->country_id,
            'invoice_footer' => $company->invoice_footer ?: '',
            'invoice_labels' => $company->invoice_labels ?: '',
            'show_item_taxes' => (bool)$company->show_item_taxes,
            'military_time' => (bool)$company->military_time,
            'fill_products' => (bool)$company->fill_products,
            'tax_name1' => $company->tax_name1 ?: '',
            'tax_rate1' => (float)$company->tax_rate1,
            'tax_name2' => $company->tax_name2 ?: '',
            'tax_rate2' => (float)$company->tax_rate2,
            'quote_terms' => $company->quote_terms ?: '',
            'show_currency_code' => (bool)$company->show_currency_code,
            'enable_second_tax_rate' => (bool)$company->enable_second_tax_rate,
            'start_of_week' => (int)$company->start_of_week,
            'financial_year_start' => (int)$company->financialYearStartMonth(),
            'enabled_modules' => (int)$company->enabled_modules,
            'payment_terms' => (int)$company->payment_terms,
            'payment_type_id' => (int)$company->payment_type_id,
            'task_rate' => (float)$company->task_rate,
            'inclusive_taxes' => (bool)$company->inclusive_taxes,
            'convert_products' => (bool)$company->convert_products,
            'custom_invoice_taxes1' => (bool)$company->custom_invoice_taxes1,
            'custom_invoice_taxes2' => (bool)$company->custom_invoice_taxes1,
            'custom_fields' => $company->custom_fields ?: '',
            'invoice_fields' => $company->invoice_fields ?: '',
            'custom_messages' => $company->custom_messages,
            'email_footer' => $company->getEmailFooter(),
            'email_subject_invoice' => $company->getEmailSubject(ENTITY_INVOICE),
            'email_subject_quote' => $company->getEmailSubject(ENTITY_QUOTE),
            'email_subject_payment' => $company->getEmailSubject(ENTITY_PAYMENT),
            'email_template_invoice' => $company->getEmailTemplate(ENTITY_INVOICE),
            'email_template_quote' => $company->getEmailTemplate(ENTITY_QUOTE),
            'email_template_payment' => $company->getEmailTemplate(ENTITY_PAYMENT),
            'email_subject_reminder1' => $company->getEmailSubject('reminder1'),
            'email_subject_reminder2' => $company->getEmailSubject('reminder2'),
            'email_subject_reminder3' => $company->getEmailSubject('reminder3'),
            'email_template_reminder1' => $company->getEmailTemplate('reminder1'),
            'email_template_reminder2' => $company->getEmailTemplate('reminder2'),
            'email_template_reminder3' => $company->getEmailTemplate('reminder3'),
            'has_custom_design1' => (bool)$company->custom_design1,
            'has_custom_design2' => (bool)$company->custom_design2,
            'has_custom_design3' => (bool)$company->custom_design3,
            'enable_portal_password' => (bool)$company->enable_portal_password,
        ];
    }
}
