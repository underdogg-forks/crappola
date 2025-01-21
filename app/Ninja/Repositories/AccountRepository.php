<?php

namespace App\Ninja\Repositories;

use App\Libraries\Utils;
use App\Models\AccountEmailSettings;
use App\Models\AccountGateway;
use App\Models\AccountTicketSettings;
use App\Models\AccountToken;
use App\Models\Client;
use App\Models\Company;
use App\Models\CompanyPlan;
use App\Models\Contact;
use App\Models\Credit;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LookupUser;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Schema;
use stdClass;
use URL;
use Illuminate\Support\Facades\Validator;

class AccountRepository
{
    public function getSearchData($user)
    {
        $data = $this->getAccountSearchData($user);

        $data['navigation'] = $user->is_admin ? $this->getNavigationSearchData() : [];

        return $data;
    }

    private function getAccountSearchData($user)
    {
        $company = $user->company;

        $data = [
            'clients'  => [],
            'contacts' => [],
            'invoices' => [],
            'quotes'   => [],
        ];

        // include custom client fields in search
        if ($company->customLabel('client1')) {
            $data[$company->present()->customLabel('client1')] = [];
        }
        if ($company->customLabel('client2')) {
            $data[$company->present()->customLabel('client2')] = [];
        }
        if ($company->customLabel('invoice_text1')) {
            $data[$company->present()->customLabel('invoice_text1')] = [];
        }
        if ($company->customLabel('invoice_text2')) {
            $data[$company->present()->customLabel('invoice_text2')] = [];
        }

        if ($user->hasPermission(['view_client', 'view_invoice'], true)) {
            $clients = Client::scope()
                ->with('contacts', 'invoices')
                ->withTrashed()
                ->with(['contacts', 'invoices' => function ($query): void {
                    $query->withTrashed();
                }])->get();
        } else {
            $clients = Client::scope()
                ->where('user_id', '=', $user->id)
                ->withTrashed()
                ->with(['contacts', 'invoices' => function ($query) use ($user): void {
                    $query->withTrashed()
                        ->where('user_id', '=', $user->id);
                }])->get();
        }

        foreach ($clients as $client) {
            if (! $client->is_deleted) {
                if ($client->name) {
                    $data['clients'][] = [
                        'value'  => ($client->id_number ? $client->id_number . ': ' : '') . $client->name,
                        'tokens' => implode(',', [$client->name, $client->id_number, $client->vat_number, $client->work_phone]),
                        'url'    => $client->present()->url,
                    ];
                }

                if ($client->custom_value1) {
                    $data[$company->present()->customLabel('client1')][] = [
                        'value'  => "{$client->custom_value1}: " . $client->getDisplayName(),
                        'tokens' => $client->custom_value1,
                        'url'    => $client->present()->url,
                    ];
                }
                if ($client->custom_value2) {
                    $data[$company->present()->customLabel('client2')][] = [
                        'value'  => "{$client->custom_value2}: " . $client->getDisplayName(),
                        'tokens' => $client->custom_value2,
                        'url'    => $client->present()->url,
                    ];
                }

                foreach ($client->contacts as $contact) {
                    $data['contacts'][] = [
                        'value'  => $contact->getSearchName(),
                        'tokens' => implode(',', [$contact->first_name, $contact->last_name, $contact->email, $contact->phone]),
                        'url'    => $client->present()->url,
                    ];
                }
            }

            foreach ($client->invoices as $invoice) {
                $entityType = $invoice->getEntityType();
                $data["{$entityType}s"][] = [
                    'value'  => $invoice->getDisplayName() . ': ' . $client->getDisplayName(),
                    'tokens' => implode(',', [$invoice->invoice_number, $invoice->po_number]),
                    'url'    => $invoice->present()->url,
                ];

                if ($customValue = $invoice->custom_text_value1) {
                    $data[$company->present()->customLabel('invoice_text1')][] = [
                        'value'  => "{$customValue}: {$invoice->getDisplayName()}",
                        'tokens' => $customValue,
                        'url'    => $invoice->present()->url,
                    ];
                }
                if ($customValue = $invoice->custom_text_value2) {
                    $data[$company->present()->customLabel('invoice_text2')][] = [
                        'value'  => "{$customValue}: {$invoice->getDisplayName()}",
                        'tokens' => $customValue,
                        'url'    => $invoice->present()->url,
                    ];
                }
            }
        }

        return $data;
    }

    private function getNavigationSearchData()
    {
        $entityTypes = [
            ENTITY_INVOICE,
            ENTITY_CLIENT,
            ENTITY_QUOTE,
            ENTITY_TASK,
            ENTITY_EXPENSE,
            ENTITY_EXPENSE_CATEGORY,
            ENTITY_VENDOR,
            ENTITY_RECURRING_INVOICE,
            ENTITY_RECURRING_QUOTE,
            ENTITY_PAYMENT,
            ENTITY_CREDIT,
            ENTITY_PROJECT,
            ENTITY_PROPOSAL,
        ];

        foreach ($entityTypes as $entityType) {
            $features[] = [
                "new_{$entityType}",
                Utils::pluralizeEntityType($entityType) . '/create',
            ];
            $features[] = [
                'list_' . Utils::pluralizeEntityType($entityType),
                Utils::pluralizeEntityType($entityType),
            ];
        }

        $features = array_merge($features, [
            ['dashboard', '/dashboard'],
            ['reports', '/reports'],
            ['calendar', '/calendar'],
            ['kanban', '/tasks/kanban'],
            ['customize_design', '/settings/customize_design'],
            ['new_tax_rate', '/tax_rates/create'],
            ['new_product', '/products/create'],
            ['new_user', '/users/create'],
            ['custom_fields', '/settings/invoice_settings'],
            ['invoice_number', '/settings/invoice_settings'],
            ['buy_now_buttons', '/settings/client_portal#buy_now'],
            ['invoice_fields', '/settings/invoice_design#invoice_fields'],
        ]);

        $settings = array_merge(Company::$basicSettings, Company::$advancedSettings);

        if (! Utils::isNinjaProd()) {
            $settings[] = ACCOUNT_SYSTEM_SETTINGS;
        }

        foreach ($settings as $setting) {
            $features[] = [
                $setting,
                "/settings/{$setting}",
            ];
        }

        foreach ($features as $feature) {
            $data[] = [
                'value'  => trans('texts.' . $feature[0]),
                'tokens' => trans('texts.' . $feature[0]),
                'url'    => URL::to($feature[1]),
            ];
        }

        return $data;
    }

    public function enablePlan($plan, $credit = 0)
    {
        $company = Auth::user()->company;
        $client = $this->getNinjaClient($company);
        $invitation = $this->createNinjaInvoice($client, $company, $plan, $credit);

        return $invitation;
    }

    public function getNinjaClient($company)
    {
        $company->load('users');
        $ninjaAccount = $this->getNinjaAccount();
        $ninjaUser = $ninjaAccount->getPrimaryUser();
        $client = Client::whereCompanyPlanId($ninjaAccount->id)
            ->wherePublicId($company->id)
            ->first();

        if (! $client) {
            $client = new Client();
            $client->public_id = $company->id;
            $client->company_id = $ninjaAccount->id;
            $client->user_id = $ninjaUser->id;
            $client->currency_id = 1;
            foreach (['name', 'address1', 'address2', 'city', 'state', 'postal_code', 'country_id', 'work_phone', 'language_id', 'vat_number'] as $field) {
                $client->$field = $company->$field;
            }
            $client->save();
            $contact = new Contact();
            $contact->user_id = $ninjaUser->id;
            $contact->company_id = $ninjaAccount->id;
            $contact->public_id = $company->id;
            $contact->contact_key = strtolower(str_random(RANDOM_KEY_LENGTH));
            $contact->is_primary = true;
            foreach (['first_name', 'last_name', 'email', 'phone'] as $field) {
                $contact->$field = $company->users()->first()->$field;
            }
            $client->contacts()->save($contact);
        }

        return $client;
    }

    public function getNinjaAccount()
    {
        $company = Company::where('account_key', 'LIKE', substr(NINJA_ACCOUNT_KEY, 0, 30) . '%')->orderBy('id')->first();

        if ($company) {
            return $company;
        }
        /*$companyPlan = new CompanyPlan();
        $companyPlan->save();*/

        $company = new company();
        $company->name = 'Invoice Ninja';
        $company->work_email = 'contact@invoiceninja.com';
        $company->work_phone = '(800) 763-1948';
        $company->account_key = NINJA_ACCOUNT_KEY;
        $company->company_id = $companyPlan->id;
        $company->save();

        $emailSettings = new AccountEmailSettings();
        $company->account_email_settings()->save($emailSettings);

        $user = new User();
        $user->registered = true;
        $user->confirmed = true;
        $user->email = NINJA_ACCOUNT_EMAIL;
        $user->username = NINJA_ACCOUNT_EMAIL;
        $user->password = strtolower(str_random(RANDOM_KEY_LENGTH));
        $user->first_name = 'Invoice';
        $user->last_name = 'Ninja';
        $user->notify_sent = true;
        $user->notify_paid = true;
        $company->users()->save($user);

        $company_ticket_settings = new AccountTicketSettings();
        $company_ticket_settings->ticket_master_id = $user->id;
        $company->company_ticket_settings()->save($company_ticket_settings);

        if ($config = env(NINJA_GATEWAY_CONFIG)) {
            $companyGateway = new AccountGateway();
            $companyGateway->user_id = $user->id;
            $companyGateway->gateway_id = NINJA_GATEWAY_ID;
            $companyGateway->public_id = 1;
            $companyGateway->setConfig(json_decode($config));
            $company->account_gateways()->save($companyGateway);
        }

        return $company;
    }

    public function save($data, $company): void
    {
        $company->fill($data);
        $company->save();
    }

    public function createNinjaInvoice($client, $clientAccount, $plan, $credit = 0)
    {
        $term = $plan['term'];
        $plan_cost = $plan['price'];
        $num_users = $plan['num_users'];
        $plan = $plan['plan'];

        if ($credit < 0) {
            $credit = 0;
        }

        $company = $this->getNinjaAccount();
        $lastInvoice = Invoice::withTrashed()->whereCompanyPlanId($company->id)->orderBy('public_id', 'DESC')->first();
        $renewalDate = $clientAccount->getRenewalDate();
        $publicId = $lastInvoice ? ($lastInvoice->public_id + 1) : 1;

        $invoice = new Invoice();
        $invoice->is_public = true;
        $invoice->company_id = $company->id;
        $invoice->user_id = $company->users()->first()->id;
        $invoice->public_id = $publicId;
        $invoice->client_id = $client->id;
        $invoice->invoice_number = $company->getNextNumber($invoice);
        $invoice->invoice_date = $renewalDate->format('Y-m-d');
        $invoice->amount = $invoice->balance = $plan_cost - $credit;
        $invoice->invoice_type_id = INVOICE_TYPE_STANDARD;

        // check for promo/discount
        $clientCompanyPlan = $clientAccount->companyPlan;
        if ($clientCompanyPlan->hasActivePromo() || $clientCompanyPlan->hasActiveDiscount($renewalDate)) {
            $discount = $invoice->amount * $clientCompanyPlan->discount;
            $invoice->discount = $clientCompanyPlan->discount * 100;
            $invoice->amount -= $discount;
            $invoice->balance -= $discount;
        }

        $invoice->save();

        if ($credit) {
            $credit_item = InvoiceItem::createNew($invoice);
            $credit_item->qty = 1;
            $credit_item->cost = -$credit;
            $credit_item->notes = trans('texts.plan_credit_description');
            $credit_item->product_key = trans('texts.plan_credit_product');
            $invoice->invoice_items()->save($credit_item);
        }

        $item = InvoiceItem::createNew($invoice);
        $item->qty = 1;
        $item->cost = $plan_cost;
        $item->notes = trans("texts.{$plan}_plan_{$term}_description");

        if ($plan == PLAN_ENTERPRISE) {
            $min = Utils::getMinNumUsers($num_users);
            $item->notes .= "\n\n###" . trans('texts.min_to_max_users', ['min' => $min, 'max' => $num_users]);
        }

        // Don't change this without updating the regex in PaymentService->createPayment()
        $item->product_key = 'Plan - ' . ucfirst($plan) . ' (' . ucfirst($term) . ')';
        $invoice->invoice_items()->save($item);

        $invitation = Invitation::createNew($invoice);
        $invitation->invoice_id = $invoice->id;
        $invitation->contact_id = $client->contacts()->first()->id;
        $invitation->invitation_key = strtolower(str_random(RANDOM_KEY_LENGTH));
        $invitation->save();

        return $invitation;
    }

    public function createNinjaCredit($client, $amount)
    {
        $company = $this->getNinjaAccount();

        $lastCredit = Credit::withTrashed()->whereCompanyPlanId($company->id)->orderBy('public_id', 'DESC')->first();
        $publicId = $lastCredit ? ($lastCredit->public_id + 1) : 1;

        $credit = new Credit();
        $credit->public_id = $publicId;
        $credit->company_id = $company->id;
        $credit->user_id = $company->users()->first()->id;
        $credit->client_id = $client->id;
        $credit->amount = $amount;
        $credit->save();

        return $credit;
    }

    public function findByKey($key)
    {
        $company = Company::whereAccountKey($key)
            ->with('clients.invoices.invoice_items', 'clients.contacts')
            ->firstOrFail();

        return $company;
    }

    public function unlinkUserFromOauth($user): void
    {
        $user->oauth_provider_id = null;
        $user->oauth_user_id = null;
        $user->save();
    }

    public function updateUserFromOauth($user, $firstName, $lastName, $email, $providerId, $oauthUserId)
    {
        if (! LookupUser::validateField('oauth_user_key', $providerId . '-' . $oauthUserId)) {
            return trans('texts.oauth_taken');
        }

        // TODO remove once multi-db is enabled
        if (User::whereOauthUserId($oauthUserId)->count() > 0) {
            return trans('texts.oauth_taken');
        }

        if (! $user->registered) {
            $rules = ['email' => 'email|required|unique:users,email,' . $user->id . ',id'];
            $validator = Validator::make(['email' => $email], $rules);

            if ($validator->fails()) {
                $messages = $validator->messages();

                return $messages->first('email');
            }

            if (! LookupUser::validateField('email', $email, $user)) {
                return trans('texts.email_taken');
            }

            $user->email = $email;
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->registered = true;

            $user->company->startTrial(PLAN_PRO);
        }

        $user->oauth_provider_id = $providerId;
        $user->oauth_user_id = $oauthUserId;
        $user->save();

        return true;
    }

    public function registerNinjaUser($user)
    {
        if (! $user || $user->email == TEST_USERNAME) {
            return false;
        }

        $url = (Utils::isNinjaDev() ? SITE_URL : NINJA_APP_URL) . '/signup/register';
        $data = '';
        $fields = [
            'first_name' => urlencode($user->first_name),
            'last_name'  => urlencode($user->last_name),
            'email'      => urlencode($user->email),
        ];

        foreach ($fields as $key => $value) {
            $data .= $key . '=' . $value . '&';
        }
        rtrim($data, '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    public function findUserByOauth($providerId, $oauthUserId)
    {
        return User::where('oauth_user_id', $oauthUserId)
            ->where('oauth_provider_id', $providerId)
            ->first();
    }

    public function findUser($user, $companyKey)
    {
        $users = $this->findUsers($user, 'company');

        foreach ($users as $user) {
            if ($companyKey && hash_equals($user->company->account_key, $companyKey)) {
                return $user;
            }
        }

        return false;
    }

    public function findUsers($user, $with = null)
    {
        $companys = $this->findUserAccounts($user->id);

        if ($companys) {
            return $this->getUserAccounts($companys, $with);
        }

        return [$user];
    }

    public function findUserAccounts($userId1, $userId2 = false)
    {
        if (! Schema::hasTable('user_accounts')) {
            return false;
        }

        $query = UserAccount::where('user_id1', '=', $userId1)
            ->orWhere('user_id2', '=', $userId1)
            ->orWhere('user_id3', '=', $userId1)
            ->orWhere('user_id4', '=', $userId1)
            ->orWhere('user_id5', '=', $userId1);

        if ($userId2) {
            $query->orWhere('user_id1', '=', $userId2)
                ->orWhere('user_id2', '=', $userId2)
                ->orWhere('user_id3', '=', $userId2)
                ->orWhere('user_id4', '=', $userId2)
                ->orWhere('user_id5', '=', $userId2);
        }

        return $query->first(['id', 'user_id1', 'user_id2', 'user_id3', 'user_id4', 'user_id5']);
    }

    public function getUserAccounts($record, $with = null)
    {
        if (! $record) {
            return false;
        }

        $userIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $field = "user_id$i";
            if ($record->$field) {
                $userIds[] = $record->$field;
            }
        }

        $users = User::with('company')
            ->whereIn('id', $userIds);

        if ($with) {
            $users->with($with);
        }

        return $users->get();
    }

    public function associateAccounts($userId1, $userId2)
    {
        $record = self::findUserAccounts($userId1, $userId2);

        if ($record) {
            foreach ([$userId1, $userId2] as $userId) {
                if (! $record->hasUserId($userId)) {
                    $record->setUserId($userId);
                }
            }
        } else {
            $record = new UserAccount();
            $record->user_id1 = $userId1;
            $record->user_id2 = $userId2;
        }

        $record->save();

        return $this->loadAccounts($userId1);
    }

    public function loadAccounts($userId)
    {
        $record = self::findUserAccounts($userId);

        return self::prepareUsersData($record);
    }

    public function prepareUsersData($record)
    {
        if (! $record) {
            return false;
        }

        $users = $this->getUserAccounts($record);

        $data = [];
        foreach ($users as $user) {
            $item = new stdClass();
            $item->id = $record->id;
            $item->user_id = $user->id;
            $item->public_id = $user->public_id;
            $item->user_name = $user->getDisplayName();
            $item->company_id = $user->company->id;
            $item->company_name = $user->company->getDisplayName();
            $item->logo_url = $user->company->hasLogo() ? $user->company->getLogoUrl() : null;
            $data[] = $item;
        }

        return $data;
    }

    public function unlinkAccount($company): void
    {
        foreach ($company->users as $user) {
            if ($userAccount = self::findUserAccounts($user->id)) {
                $userAccount->removeUserId($user->id);
                $userAccount->save();
            }
        }
    }

    public function unlinkUser($userAccountId, $userId): void
    {
        $userAccount = UserAccount::whereId($userAccountId)->first();
        if ($userAccount->hasUserId($userId)) {
            $userAccount->removeUserId($userId);
            $userAccount->save();
        }

        $user = User::whereId($userId)->first();

        if (! $user->public_id && $user->company->hasMultipleAccounts()) {
            $companyPlan = CompanyPlan::create();
            $companyPlan->save();
            $user->company->company_id = $companyPlan->id;
            $user->company->save();
        }
    }

    public function create($firstName = '', $lastName = '', $email = '', $password = '', $companyPlan = false)
    {
        //if (!$companyPlan) {
        /*if (Utils::isNinja()) {
            $this->checkForSpammer();
        }*/

        /*$companyPlan = new CompanyPlan();
        $companyPlan->utm_source = request()->get('utm_source');
        $companyPlan->utm_medium = request()->get('utm_medium');
        $companyPlan->utm_campaign = request()->get('utm_campaign');
        $companyPlan->utm_term = request()->get('utm_term');
        $companyPlan->utm_content = request()->get('utm_content');
        $companyPlan->referral_code = Session::get(SESSION_REFERRAL_CODE);*/

        /*if (request()->get('utm_campaign')) {
            if (env('PROMO_CAMPAIGN') && hash_equals(request()->get('utm_campaign'), env('PROMO_CAMPAIGN'))) {
                $companyPlan->applyDiscount(.75);
            } elseif (env('PARTNER_CAMPAIGN') && hash_equals(request()->get('utm_campaign'), env('PARTNER_CAMPAIGN'))) {
                $companyPlan->applyFreeYear();
            } elseif (env('EDUCATION_CAMPAIGN') && hash_equals(request()->get('utm_campaign'), env('EDUCATION_CAMPAIGN'))) {
                $companyPlan->applyFreeYear(2);
            }
        }*/
        //$companyPlan->applyDiscount(.5);
        //session()->flash('warning', $companyPlan->present()->promoMessage());

        // $companyPlan->save();
        //}

        $company = new Company();
        $company->ip = request()->getClientIp();
        $company->account_key = strtolower(str_random(RANDOM_KEY_LENGTH));
        //$company->company_id = $companyPlan->id;
        $company->currency_id = DEFAULT_CURRENCY;

        // Set default language/currency based on IP
        // TODO Disabled until GDPR implications are understood
        /*
        if (\Cache::get('currencies')) {
            if ($data = unserialize(@file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $company->ip))) {
                $currencyCode = strtolower($data['geoplugin_currencyCode']);
                $countryCode = strtolower($data['geoplugin_countryCode']);

                $currency = \Cache::get('currencies')->filter(function ($item) use ($currencyCode) {
                    return strtolower($item->code) == $currencyCode;
                })->first();
                if ($currency) {
                    $company->currency_id = $currency->id;
                }

                $country = \Cache::get('countries')->filter(function ($item) use ($countryCode) {
                    return strtolower($item->iso_3166_2) == $countryCode || strtolower($item->iso_3166_3) == $countryCode;
                })->first();
                if ($country) {
                    $company->country_id = $country->id;
                }

                $language = \Cache::get('languages')->filter(function ($item) use ($countryCode) {
                    return strtolower($item->locale) == $countryCode;
                })->first();
                if ($language) {
                    $company->language_id = $language->id;
                }
            }
        }
        */

        $company->save();

        $user = new User();
        if (! $firstName && ! $lastName && ! $email && ! $password) {
            $user->password = strtolower(str_random(RANDOM_KEY_LENGTH));
            $user->username = strtolower(str_random(RANDOM_KEY_LENGTH));
        } else {
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->email = $user->username = $email;
            if (! $password) {
                $password = strtolower(str_random(RANDOM_KEY_LENGTH));
            }
            $user->password = bcrypt('password');
        }

        $user->confirmed = ! Utils::isNinja();
        $user->registered = ! Utils::isNinja() || $email;

        if (! $user->confirmed) {
            $user->confirmation_code = strtolower(str_random(RANDOM_KEY_LENGTH));
        }

        $company->users()->save($user);

        //$emailSettings = new AccountEmailSettings();
        //$company->account_email_settings()->save($emailSettings);

        $companyTicketSettings = new AccountTicketSettings();
        $companyTicketSettings->company_id = 1;
        $companyTicketSettings->ticket_master_id = $user->id;
        $companyTicketSettings->ticket_number_start = 1;

        $company->company_ticket_settings()->save($companyTicketSettings);

        return $company;
    }

    private function checkForSpammer(): void
    {
        $ip = request()->getClientIp();

        // Apple's IP for their test companies
        if ($ip == '17.200.11.44') {
            return;
        }

        $count = Company::whereIp($ip)->whereHas('users', function ($query): void {
            $query->whereRegistered(true);
        })->count();

        if ($count >= 15) {
            abort();
        }
    }

    public function findWithReminders()
    {
        return Company::whereHas('account_email_settings', function ($query): void {
            $query->whereRaw('enable_reminder1 = 1 OR enable_reminder2 = 1 OR enable_reminder3 = 1 OR enable_reminder4 = 1 OR enable_quote_reminder1 = 1 OR enable_quote_reminder2 = 1 OR enable_quote_reminder3 = 1 OR enable_quote_reminder4 = 1');
        })->get();
    }

    public function findWithFees()
    {
        return Company::whereHas('account_email_settings', function ($query): void {
            $query->where('late_fee1_amount', '>', 0)
                ->orWhere('late_fee1_percent', '>', 0)
                ->orWhere('late_fee2_amount', '>', 0)
                ->orWhere('late_fee2_percent', '>', 0)
                ->orWhere('late_fee3_amount', '>', 0)
                ->orWhere('late_fee3_percent', '>', 0)
                ->orWhere('late_fee_quote1_amount', '>', 0)
                ->orWhere('late_fee_quote1_percent', '>', 0)
                ->orWhere('late_fee_quote2_amount', '>', 0)
                ->orWhere('late_fee_quote2_percent', '>', 0)
                ->orWhere('late_fee_quote3_amount', '>', 0)
                ->orWhere('late_fee_quote3_percent', '>', 0);
        })->get();
    }

    public function createTokens($user, $name): void
    {
        $name = trim($name) ?: 'TOKEN';
        $users = $this->findUsers($user);

        foreach ($users as $user) {
            if ($token = AccountToken::whereUserId($user->id)->whereName($name)->first()) {
                continue;
            }

            $token = AccountToken::createNew($user);
            $token->name = $name;
            $token->token = strtolower(str_random(RANDOM_KEY_LENGTH));
            $token->save();
        }
    }

    public function getUserAccountId($company)
    {
        $user = $company->users()->first();
        $userAccount = $this->findUserAccounts($user->id);

        return $userAccount ? $userAccount->id : false;
    }
}
