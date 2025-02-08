<?php
namespace App\Ninja\Transformers;

use App\Models\Account;
use App\Models\User;

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

    public function __construct(Account $account, $serializer, $tokenName)
    {
        parent::__construct($account, $serializer);
        $this->tokenName = $tokenName;
    }

    public function includeUser(User $user)
    {
        $transformer = new UserTransformer($this->account, $this->serializer);
        return $this->includeItem($user, $transformer, 'user');
    }

    public function transform(User $user)
    {
        return [
            'account_key' => $user->account->account_key,
            'name' => $user->account->present()->name,
            'token' => $user->account->getToken($user->id, $this->tokenName),
            'default_url' => SITE_URL,
            'plan' => $user->account->company->plan,
            'logo' => $user->account->logo,
            'logo_url' => $user->account->getLogoURL(),
        ];
    }
}
