<?php

namespace App\Http\Controllers;

use App\Events\SubdomainWasRemoved;
use App\Events\SubdomainWasUpdated;
use App\Events\UserSettingsChanged;
use App\Events\UserSignedUp;
use App\Http\Requests\SaveClientPortalSettings;
use App\Http\Requests\SaveEmailSettings;
use App\Http\Requests\SaveTicketSettings;
use App\Http\Requests\UpdateAccountRequest;
use App\Jobs\PurgeAccountData;
use App\Jobs\PurgeClientData;
use App\Libraries\Utils;
use App\Models\AccountEmailSettings;
use App\Models\AccountGateway;
use App\Models\AccountTicketSettings;
use App\Models\Affiliate;
use App\Models\Company;
use App\Models\Document;
use App\Models\EntityModel;
use App\Models\Gateway;
use App\Models\GatewayType;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Models\InvoiceDesign;
use App\Models\License;
use App\Models\LookupAccount;
use App\Models\LookupUser;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\TicketTemplate;
use App\Models\User;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Repositories\ReferralRepository;
use App\Services\AuthService;
use App\Services\PaymentService;
use App\Services\TemplateService;
use Cache;
use Exception;
use File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Image;
use Illuminate\Support\Facades\Input;
use Log;
use stdClass;
use URL;
use Illuminate\Support\Facades\Validator;

//use Nwidart\Modules\Facades\Module;

/**
 * Class AccountController.
 */
class CompanyController extends BaseController
{
    /**
     * @var AccountRepository
     */
    protected $companyRepo;

    protected UserMailer $userMailer;

    protected ContactMailer $contactMailer;

    protected ReferralRepository $referralRepository;

    protected PaymentService $paymentService;

    /**
     * AccountController constructor.
     */
    public function __construct(
        AccountRepository $companyRepo,
        UserMailer $userMailer,
        ContactMailer $contactMailer,
        ReferralRepository $referralRepository,
        PaymentService $paymentService
    ) {
        $this->accountRepo = $companyRepo;
        $this->userMailer = $userMailer;
        $this->contactMailer = $contactMailer;
        $this->referralRepository = $referralRepository;
        $this->paymentService = $paymentService;
    }

    /**
     * @return RedirectResponse
     */
    public function getStarted()
    {
        $user = false;
        $company = false;
        $guestKey = $request->get('guest_key'); // local storage key to login until registered

        if (Auth::check()) {
            return Redirect::to('invoices/create');
        }

        if (! Utils::isNinja() && Company::count() > 0) {
            return Redirect::to('/login');
        }

        if ($guestKey) {
            $user = User::where('password', '=', $guestKey)->first();

            if ($user && $user->registered) {
                return Redirect::to('/');
            }
        }

        if (! $user) {
            $company = $this->accountRepo->create();
            $user = $company->users()->first();
        }

        Auth::login($user, true);
        event(new UserSignedUp());

        if ($company && $company->language_id && $company->language_id != DEFAULT_LANGUAGE) {
            $link = link_to('/invoices/create?lang=en', 'click here');
            $message = sprintf('Your company language has been set automatically, %s to change to English', $link);
            Session::flash('warning', $message);
        }

        if ($redirectTo = $request->get('redirect_to')) {
            $redirectTo = SITE_URL . '/' . ltrim($redirectTo, '/');
        } else {
            $redirectTo = $request->get('sign_up') ? 'dashboard' : 'invoices/create';
        }

        return Redirect::to($redirectTo)->with('sign_up', $request->get('sign_up'));
    }

    /**
     * @return RedirectResponse
     */
    public function changePlan()
    {
        $user = Auth::user();
        $company = $user->company;
        $companyPlan = $company->companyPlan;

        $plan = $request->get('plan');
        $term = $request->get('plan_term');
        $numUsers = $request->get('num_users');

        if ($plan != PLAN_ENTERPRISE) {
            $numUsers = 1;
        }

        $planDetails = $company->getPlanDetails(false, false);

        $newPlan = [
            'plan'      => $plan,
            'term'      => $term,
            'num_users' => $numUsers,
        ];
        $newPlan['price'] = Utils::getPlanPrice($newPlan);
        $credit = 0;

        if ($plan == PLAN_FREE && $companyPlan->processRefund(Auth::user())) {
            Session::flash('warning', trans('texts.plan_refunded'));
        }

        if ($companyPlan->payment && ! empty($planDetails['paid']) && $plan != PLAN_FREE) {
            $time_used = $planDetails['paid']->diff(date_create());
            $days_used = $time_used->days;

            if ($time_used->invert) {
                // They paid in advance
                $days_used *= -1;
            }

            $days_total = $planDetails['paid']->diff($planDetails['expires'])->days;
            $percent_used = $days_used / $days_total;
            $credit = round(floatval($companyPlan->payment->amount) * (1 - $percent_used), 2);
        }

        if ($newPlan['price'] > $credit) {
            $invitation = $this->accountRepo->enablePlan($newPlan, $credit);

            return Redirect::to('view/' . $invitation->invitation_key);
        }
        if ($plan == PLAN_FREE) {
            $companyPlan->discount = 0;

            $ninjaClient = $this->accountRepo->getNinjaClient($company);
            $ninjaClient->send_reminders = false;
            $ninjaClient->save();
        } else {
            $companyPlan->plan_term = $term;
            $companyPlan->plan_price = $newPlan['price'];
            $companyPlan->num_users = $numUsers;
            $companyPlan->plan_expires = date_create()->modify($term == PLAN_TERM_MONTHLY ? '+1 month' : '+1 year')->format('Y-m-d');
        }

        $companyPlan->trial_plan = null;
        $companyPlan->plan = $plan;
        $companyPlan->save();

        Session::flash('message', trans('texts.updated_plan'));

        return Redirect::to('settings/account_management');
    }

    /**
     * @param       $visible
     * @param mixed $filter
     *
     * @return mixed
     */
    public function setEntityFilter($entityType, $filter = '')
    {
        if ($filter == 'true') {
            $filter = '';
        }

        // separate state and status filters
        $filters = explode(',', $filter);
        $stateFilter = [];
        $statusFilter = [];
        foreach ($filters as $filter) {
            if (in_array($filter, EntityModel::$statuses)) {
                $stateFilter[] = $filter;
            } else {
                $statusFilter[] = $filter;
            }
        }

        Session::put("entity_state_filter:{$entityType}", implode(',', $stateFilter));
        Session::put("entity_status_filter:{$entityType}", implode(',', $statusFilter));

        return RESULT_SUCCESS;
    }

    public function getSearchData()
    {
        $data = $this->accountRepo->getSearchData(Auth::user());

        return Response::json($data);
    }

    /**
     * @param bool $section
     *
     * @return \Illuminate\Contracts\View\View|RedirectResponse
     */
    public function showSection($section = false)
    {
        if (! $section) {
            return Redirect::to('/settings/' . ACCOUNT_COMPANY_DETAILS, 301);
        }
        if ($section == ACCOUNT_COMPANY_DETAILS) {
            return self::showCompanyPlanDetails();
        }

        if ($section == ACCOUNT_LOCALIZATION) {
            return self::showLocalization();
        } elseif ($section == ACCOUNT_PAYMENTS) {
            return self::showOnlinePayments();
        } elseif ($section == ACCOUNT_BANKS) {
            return self::showBankAccounts();
        } elseif ($section == ACCOUNT_INVOICE_SETTINGS) {
            return self::showInvoiceSettings();
        } elseif ($section == ACCOUNT_IMPORT_EXPORT) {
            return View::make('companies.import_export', [
                'title' => trans('texts.import_export'),
            ]);
        } elseif ($section == ACCOUNT_MANAGEMENT) {
            return self::showAccountManagement();
        } elseif ($section == ACCOUNT_INVOICE_DESIGN || $section == ACCOUNT_CUSTOMIZE_DESIGN) {
            return self::showInvoiceDesign($section);
        } elseif ($section == ACCOUNT_CLIENT_PORTAL) {
            return self::showClientPortal();
        } elseif ($section === ACCOUNT_TEMPLATES_AND_REMINDERS) {
            return self::showTemplates();
        } elseif ($section === ACCOUNT_PRODUCTS) {
            return self::showProducts();
        } elseif ($section === ACCOUNT_TAX_RATES) {
            return self::showTaxRates();
        } elseif ($section === ACCOUNT_TICKETS) {
            return self::showTickets();
        } elseif ($section === ACCOUNT_PAYMENT_TERMS) {
            return self::showPaymentTerms();
        } elseif ($section === ACCOUNT_SYSTEM_SETTINGS) {
            return self::showSystemSettings();
        }
        $view = "companies.{$section}";
        if (! view()->exists($view)) {
            return redirect('/settings/company_details');
        }

        $data = [
            'company' => Company::with('users')->findOrFail(Auth::user()->company_id),
            'title'   => trans("texts.{$section}"),
            'section' => $section,
        ];

        return View::make($view, $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showCompanyPlanDetails()
    {
        // check that logo is less than the max file size
        $company = Auth::user()->company;
        if ($company->isLogoTooLarge()) {
            Session::flash('warning', trans('texts.logo_too_large', ['size' => $company->getLogoSize() . 'KB']));
        }

        $data = [
            'company' => Company::with('users')->findOrFail(Auth::user()->company_id),
            'sizes'   => Cache::get('sizes'),
            'title'   => trans('texts.company_details'),
        ];

        return View::make('companies.details', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showLocalization()
    {
        $data = [
            'company'         => Company::with('users')->findOrFail(Auth::user()->company_id),
            'timezones'       => Cache::get('timezones'),
            'dateFormats'     => Cache::get('dateFormats'),
            'datetimeFormats' => Cache::get('datetimeFormats'),
            'title'           => trans('texts.localization'),
            'weekdays'        => Utils::getTranslatedWeekdayNames(),
            'months'          => Utils::getMonthOptions(),
        ];

        return View::make('companies.localization', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View|RedirectResponse
     */
    private function showOnlinePayments()
    {
        $company = Auth::user()->company;
        $company->load('account_gateways');
        $count = $company->account_gateways->count();
        $trashedCount = AccountGateway::scope()->withTrashed()->count();

        if ($companyGateway = $company->getGatewayConfig(GATEWAY_STRIPE)) {
            if (! $companyGateway->getPublishableKey()) {
                Session::now('warning', trans('texts.missing_publishable_key'));
            }
        }

        $tokenBillingOptions = [];
        for ($i = 1; $i <= 4; $i++) {
            $tokenBillingOptions[$i] = trans("texts.token_billing_{$i}");
        }

        return View::make('companies.payments', [
            'showAdd'             => $count < count(Gateway::$alternate) + 1,
            'title'               => trans('texts.online_payments'),
            'tokenBillingOptions' => $tokenBillingOptions,
            'currency'            => Utils::getFromCache(Session::get(SESSION_CURRENCY, DEFAULT_CURRENCY), 'currencies'),
            'taxRates'            => TaxRate::scope()->whereIsInclusive(false)->orderBy('rate')->get(['public_id', 'name', 'rate']),
            'company'             => $company,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showBankAccounts()
    {
        $company = auth()->user()->company;

        return View::make('companies.banks', [
            'title'              => trans('texts.bank_accounts'),
            'advanced'           => ! Auth::user()->hasFeature(FEATURE_EXPENSES),
            'warnPaymentGateway' => ! $company->account_gateways->count(),
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showInvoiceSettings()
    {
        $company = Auth::user()->company;
        $recurringHours = [];

        for ($i = 0; $i < 24; $i++) {
            $format = $company->military_time ? 'H:i' : 'g:i a';
            $recurringHours[$i] = date($format, strtotime("{$i}:00"));
        }

        $data = [
            'company'        => Company::with('users')->findOrFail(Auth::user()->company_id),
            'title'          => trans('texts.invoice_settings'),
            'section'        => ACCOUNT_INVOICE_SETTINGS,
            'recurringHours' => $recurringHours,
        ];

        return View::make('companies.invoice_settings', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showAccountManagement()
    {
        $company = Auth::user()->company;
        $planDetails = $company->getPlanDetails(true, false);
        $portalLink = false;

        if (Utils::isNinja() && $planDetails
            && $company->getPrimaryAccount()->id == auth()->user()->company_id
            && $ninjaClient = $this->accountRepo->getNinjaClient($company)) {
            $contact = $ninjaClient->getPrimaryContact();
            $portalLink = $contact->link;
        }

        $data = [
            'company'     => $company,
            'portalLink'  => $portalLink,
            'planDetails' => $planDetails,
            'title'       => trans('texts.account_management'),
        ];

        return View::make('companies.management', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showInvoiceDesign($section)
    {
        $company = Auth::user()->company->load('country');

        if ($invoice = Invoice::scope()->invoices()->orderBy('id')->first()) {
            $invoice->load('company', 'client.contacts', 'invoice_items');
            $invoice->invoice_date = Utils::fromSqlDate($invoice->invoice_date);
            $invoice->due_at = Utils::fromSqlDate($invoice->due_at);
        } else {
            $client = new stdClass();
            $contact = new stdClass();
            $invoiceItem = new stdClass();
            $document = new stdClass();

            $client->name = 'Sample Client';
            $client->address1 = '10 Main St.';
            $client->city = 'New York';
            $client->state = 'NY';
            $client->postal_code = '10000';
            $client->work_phone = '(212) 555-0000';
            $client->work_email = 'sample@example.com';
            $client->balance = 100;
            $client->vat_number = $company->vat_number ? '1234567890' : '';
            $client->id_number = $company->id_number ? '1234567890' : '';

            if ($company->customLabel('client1')) {
                $client->custom_value1 = '0000';
            }
            if ($company->customLabel('client2')) {
                $client->custom_value2 = '0000';
            }

            $invoice = new stdClass();
            $invoice->invoice_number = '0000';
            $invoice->invoice_date = Utils::fromSqlDate(date('Y-m-d'));
            $invoice->company = json_decode($company->toJson());
            $invoice->amount = $invoice->balance = 100;

            if ($company->customLabel('invoice_text1')) {
                $invoice->custom_text_value1 = '0000';
            }
            if ($company->customLabel('invoice_text2')) {
                $invoice->custom_text_value2 = '0000';
            }

            $invoice->terms = trim($company->invoice_terms);
            $invoice->invoice_footer = trim($company->invoice_footer);

            $contact->first_name = 'Test';
            $contact->last_name = 'Contact';
            $contact->email = 'contact@gmail.com';
            $client->contacts = [$contact];

            $invoiceItem->cost = 100;
            $invoiceItem->qty = 1;
            $invoiceItem->notes = 'Notes';
            $invoiceItem->product_key = 'Item';
            $invoiceItem->discount = 10;
            $invoiceItem->tax_name1 = 'Tax';
            $invoiceItem->tax_rate1 = 10;

            if ($company->customLabel('product1')) {
                $invoiceItem->custom_value1 = '0000';
            }
            if ($company->customLabel('product2')) {
                $invoiceItem->custom_value2 = '0000';
            }

            $document->base64 = 'data:image/jpeg;base64,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';

            $invoice->client = $client;
            $invoice->invoice_items = [$invoiceItem];
            //$invoice->documents = $company->hasFeature(FEATURE_DOCUMENTS) ? [$document] : [];
            $invoice->documents = [];
        }

        $data['company'] = $company;
        $data['invoice'] = $invoice;
        $data['invoiceLabels'] = json_decode($company->invoice_labels) ?: [];
        $data['title'] = trans('texts.invoice_design');
        $data['invoiceDesigns'] = InvoiceDesign::getDesigns();
        $data['invoiceFonts'] = Cache::get('fonts');
        $data['section'] = $section;
        $data['pageSizes'] = array_combine(InvoiceDesign::$pageSizes, InvoiceDesign::$pageSizes);
        $data['showModuleSettings'] = Utils::hasModuleSettings();

        $design = false;
        foreach ($data['invoiceDesigns'] as $item) {
            if ($item->id == $company->invoice_design_id) {
                $design = $item->javascript;
                break;
            }
        }

        if ($section == ACCOUNT_CUSTOMIZE_DESIGN) {
            $data['customDesign'] = ($custom = $company->getCustomDesign(request()->design_id)) ? $custom : $design;
        }

        return View::make("companies.{$section}", $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showClientPortal()
    {
        $company = Auth::user()->company->load('country');
        $css = $company->client_view_css ? $company->client_view_css : '';

        if (Utils::isNinja() && $css) {
            // Unescape the CSS for display purposes
            $css = str_replace(
                ['\3C ', '\3E ', '\26 '],
                ['<', '>', '&'],
                $css
            );
        }

        $types = [
            GATEWAY_TYPE_CREDIT_CARD,
            GATEWAY_TYPE_BANK_TRANSFER,
            GATEWAY_TYPE_PAYPAL,
            GATEWAY_TYPE_BITCOIN,
            GATEWAY_TYPE_DWOLLA,
        ];
        $options = [];
        foreach ($types as $type) {
            if ($company->getGatewayByType($type)) {
                $alias = GatewayType::getAliasFromId($type);
                $options[$alias] = trans("texts.{$alias}");
            }
        }

        $data = [
            'client_view_css'        => $css,
            'enable_portal_password' => $company->enable_portal_password,
            'send_portal_password'   => $company->send_portal_password,
            'title'                  => trans('texts.client_portal'),
            'section'                => ACCOUNT_CLIENT_PORTAL,
            'company'                => $company,
            'products'               => Product::scope()->orderBy('product_key')->get(),
            'gateway_types'          => $options,
        ];

        if (Utils::isSelfHost()) {
            $js = $company->client_view_js ? $company->client_view_js : '';
            $data['client_view_js'] = $js;
        }

        return View::make('companies.client_portal', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showTemplates()
    {
        $company = Auth::user()->company->load('country');
        $data['company'] = $company;
        $data['templates'] = [];
        $data['defaultTemplates'] = [];
        foreach (AccountEmailSettings::$templates as $type) {
            $data['templates'][$type] = [
                'subject'  => $company->getEmailSubject($type),
                'template' => $company->getEmailTemplate($type),
            ];
            $data['defaultTemplates'][$type] = [
                'subject'  => $company->getDefaultEmailSubject($type),
                'template' => $company->getDefaultEmailTemplate($type),
            ];
        }
        $data['title'] = trans('texts.email_templates');
        $data['showModuleSettings'] = Utils::hasModuleSettings();

        return View::make('companies.templates_and_reminders', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showProducts()
    {
        $data = [
            'company' => Auth::user()->company,
            'title'   => trans('texts.product_library'),
        ];

        return View::make('companies.products', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showTaxRates()
    {
        $data = [
            'company'              => Auth::user()->company,
            'title'                => trans('texts.tax_rates'),
            'taxRates'             => TaxRate::scope()->whereIsInclusive(false)->get(),
            'countInvoices'        => Invoice::scope()->withTrashed()->count(),
            'hasInclusiveTaxRates' => TaxRate::scope()->whereIsInclusive(true)->count() ? true : false,
        ];

        return View::make('companies.tax_rates', $data);
    }

    /**
     * @return mixed
     */
    private function showTickets()
    {
        $data = [
            'company'                 => Auth::user()->company,
            'company_ticket_settings' => Auth::user()->company->company_ticket_settings,
            'templates'               => TicketTemplate::scope()->get(),
            'title'                   => trans('texts.ticket_settings'),
            'section'                 => ACCOUNT_TICKETS,
        ];

        return View::make('companies.tickets', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showPaymentTerms()
    {
        $data = [
            'company' => Auth::user()->company,
            'title'   => trans('texts.payment_terms'),
        ];

        return View::make('companies.payment_terms', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View|RedirectResponse
     */
    private function showSystemSettings()
    {
        if (Utils::isNinjaProd()) {
            return Redirect::to('/');
        }

        $data = [
            'company' => Company::with('users')->findOrFail(Auth::user()->company_id),
            'title'   => trans('texts.system_settings'),
            'section' => ACCOUNT_SYSTEM_SETTINGS,
        ];

        return View::make('companies.system_settings', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function showUserDetails()
    {
        if (! auth()->user()->registered) {
            return redirect('/')->withError(trans('texts.sign_up_to_save'));
        }

        $oauthLoginUrls = [];
        foreach (AuthService::$providers as $provider) {
            $oauthLoginUrls[] = ['label' => $provider, 'url' => URL::to('/auth/' . strtolower($provider))];
        }

        $data = [
            'company'           => Company::with('users')->findOrFail(Auth::user()->company_id),
            'title'             => trans('texts.user_details'),
            'user'              => Auth::user(),
            'oauthProviderName' => AuthService::getProviderName(Auth::user()->oauth_provider_id),
            'oauthLoginUrls'    => $oauthLoginUrls,
            'referralCounts'    => $this->referralRepository->getCounts(Auth::user()->referral_code),
        ];

        return View::make('companies.user_details', $data);
    }

    /**
     * @return RedirectResponse
     */
    public function doSection($section)
    {
        if ($section === ACCOUNT_LOCALIZATION) {
            return self::saveLocalization();
        }
        if ($section == ACCOUNT_PAYMENTS) {
            return self::saveOnlinePayments();
        } elseif ($section === ACCOUNT_NOTIFICATIONS) {
            return self::saveNotifications();
        } elseif ($section === ACCOUNT_EXPORT) {
            return self::export();
        } elseif ($section === ACCOUNT_INVOICE_SETTINGS) {
            return self::saveInvoiceSettings();
        } elseif ($section === ACCOUNT_INVOICE_DESIGN) {
            return self::saveInvoiceDesign();
        } elseif ($section === ACCOUNT_CUSTOMIZE_DESIGN) {
            return self::saveCustomizeDesign();
        } elseif ($section === ACCOUNT_TEMPLATES_AND_REMINDERS) {
            return self::saveEmailTemplates();
        } elseif ($section === ACCOUNT_PRODUCTS) {
            return self::saveProducts();
        } elseif ($section === ACCOUNT_TAX_RATES) {
            return self::saveTaxRates();
        } elseif ($section === ACCOUNT_PAYMENT_TERMS) {
            return self::savePaymetTerms();
        } elseif ($section === ACCOUNT_MANAGEMENT) {
            return self::saveAccountManagement();
        }
    }

    /**
     * @return RedirectResponse
     */
    private function saveLocalization()
    {
        /** @var company $company */
        $company = Auth::user()->company;

        $company->timezone_id = $request->get('timezone_id') ? $request->get('timezone_id') : null;
        $company->date_format_id = $request->get('date_format_id') ? $request->get('date_format_id') : null;
        $company->datetime_format_id = $request->get('datetime_format_id') ? $request->get('datetime_format_id') : null;
        $company->currency_id = $request->get('currency_id') ? $request->get('currency_id') : 1; // US Dollar
        $company->language_id = $request->get('language_id') ? $request->get('language_id') : 1; // English
        $company->military_time = $request->get('military_time') ? true : false;
        $company->show_currency_code = $request->get('show_currency_code') ? true : false;
        $company->start_of_week = $request->get('start_of_week') ? $request->get('start_of_week') : 0;
        $company->financial_year_start = $request->get('financial_year_start') ? $request->get('financial_year_start') : null;
        $company->save();

        event(new UserSettingsChanged());

        Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('settings/' . ACCOUNT_LOCALIZATION);
    }

    /**
     * @return RedirectResponse
     */
    private function saveOnlinePayments()
    {
        $company = Auth::user()->company;
        $company->token_billing_type_id = $request->get('token_billing_type_id');
        $company->auto_bill_on_due_date = boolval($request->get('auto_bill_on_due_date'));
        $company->gateway_fee_enabled = boolval($request->get('gateway_fee_enabled'));
        $company->send_item_details = boolval($request->get('send_item_details'));

        $company->save();

        event(new UserSettingsChanged());

        Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('settings/' . ACCOUNT_PAYMENTS);
    }

    /**
     * @return RedirectResponse
     */
    private function saveNotifications()
    {
        $user = Auth::user();
        $user->notify_sent = $request->get('notify_sent');
        $user->notify_viewed = $request->get('notify_viewed');
        $user->notify_paid = $request->get('notify_paid');
        $user->notify_approved = $request->get('notify_approved');
        $user->only_notify_owned = $request->get('only_notify_owned');
        $user->slack_webhook_url = $request->get('slack_webhook_url');
        $user->save();

        $company = $user->company;
        $company->fill(request()->all());
        $company->save();

        Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('settings/' . ACCOUNT_NOTIFICATIONS);
    }

    /**
     * @return $this|RedirectResponse
     */
    private function saveInvoiceSettings()
    {
        if (Auth::user()->company->hasFeature(FEATURE_INVOICE_SETTINGS)) {
            $rules = [];
            foreach ([ENTITY_INVOICE, ENTITY_QUOTE, ENTITY_CLIENT] as $entityType) {
                if ($request->get("{$entityType}_number_type") == 'pattern') {
                    $rules["{$entityType}_number_pattern"] = 'has_counter';
                }
            }
            if ($request->get('credit_number_enabled')) {
                $rules['credit_number_prefix'] = 'required_without:credit_number_pattern';
                $rules['credit_number_pattern'] = 'required_without:credit_number_prefix';
            }
            $validator = Validator::make(\Request::all(), $rules);

            if ($validator->fails()) {
                return Redirect::to('settings/' . ACCOUNT_INVOICE_SETTINGS)
                    ->withErrors($validator)
                    ->withInput();
            }
            $company = Auth::user()->company;
            $company->custom_value1 = $request->get('custom_value1');
            $company->custom_value2 = $request->get('custom_value2');
            $company->custom_invoice_taxes1 = $request->get('custom_invoice_taxes1') ? true : false;
            $company->custom_invoice_taxes2 = $request->get('custom_invoice_taxes2') ? true : false;
            $company->custom_fields = request()->custom_fields;
            $company->invoice_number_padding = $request->get('invoice_number_padding');
            $company->invoice_number_counter = $request->get('invoice_number_counter');
            $company->quote_number_prefix = $request->get('quote_number_prefix');
            $company->share_counter = $request->get('share_counter') ? true : false;
            $company->invoice_terms = $request->get('invoice_terms');
            $company->invoice_footer = $request->get('invoice_footer');
            $company->quote_terms = $request->get('quote_terms');
            $company->auto_convert_quote = $request->get('auto_convert_quote');
            $company->auto_archive_quote = $request->get('auto_archive_quote');
            $company->require_approve_quote = $request->get('require_approve_quote');
            $company->allow_approve_expired_quote = $request->get('allow_approve_expired_quote');
            $company->auto_archive_invoice = $request->get('auto_archive_invoice');
            $company->auto_email_invoice = $request->get('auto_email_invoice');
            $company->recurring_invoice_number_prefix = $request->get('recurring_invoice_number_prefix');

            $company->client_number_prefix = trim($request->get('client_number_prefix'));
            $company->client_number_pattern = trim($request->get('client_number_pattern'));
            $company->client_number_counter = $request->get('client_number_counter');
            $company->credit_number_counter = $request->get('credit_number_counter');
            $company->credit_number_prefix = trim($request->get('credit_number_prefix'));
            $company->credit_number_pattern = trim($request->get('credit_number_pattern'));
            $company->reset_counter_frequency_id = $request->get('reset_counter_frequency_id');
            $company->reset_counter_date = $company->reset_counter_frequency_id ? Utils::toSqlDate($request->get('reset_counter_date')) : null;
            $company->custom_fields_options = request()->custom_fields_options;

            if (request()->has('recurring_hour')) {
                $company->recurring_hour = $request->get('recurring_hour');
            }

            if (! $company->share_counter) {
                $company->quote_number_counter = $request->get('quote_number_counter');
            }

            foreach ([ENTITY_INVOICE, ENTITY_QUOTE, ENTITY_CLIENT] as $entityType) {
                if ($request->get("{$entityType}_number_type") == 'prefix') {
                    $company->{"{$entityType}_number_prefix"} = trim($request->get("{$entityType}_number_prefix"));
                    $company->{"{$entityType}_number_pattern"} = null;
                } else {
                    $company->{"{$entityType}_number_pattern"} = trim($request->get("{$entityType}_number_pattern"));
                    $company->{"{$entityType}_number_prefix"} = null;
                }
            }

            if (! $company->share_counter
                && $company->invoice_number_prefix == $company->quote_number_prefix
                && $company->invoice_number_pattern == $company->quote_number_pattern) {
                Session::flash('error', trans('texts.invalid_counter'));

                return Redirect::to('settings/' . ACCOUNT_INVOICE_SETTINGS)->withInput();
            }
            $company->save();
            Session::flash('message', trans('texts.updated_settings'));
        }

        return Redirect::to('settings/' . ACCOUNT_INVOICE_SETTINGS);
    }

    /**
     * @return RedirectResponse
     */
    private function saveInvoiceDesign()
    {
        if (Auth::user()->company->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN)) {
            $company = Auth::user()->company;
            $company->hide_quantity = $request->get('hide_quantity') ? true : false;
            $company->hide_paid_to_date = $request->get('hide_paid_to_date') ? true : false;
            $company->all_pages_header = $request->get('all_pages_header') ? true : false;
            $company->all_pages_footer = $request->get('all_pages_footer') ? true : false;
            $company->invoice_embed_documents = $request->get('invoice_embed_documents') ? true : false;
            $company->header_font_id = $request->get('header_font_id');
            $company->body_font_id = $request->get('body_font_id');
            $company->primary_color = $request->get('primary_color');
            $company->secondary_color = $request->get('secondary_color');
            $company->invoice_design_id = $request->get('invoice_design_id');
            $company->quote_design_id = $request->get('quote_design_id');
            $company->font_size = intval($request->get('font_size'));
            $company->page_size = $request->get('page_size');
            $company->background_image_id = Document::getPrivateId(request()->background_image_id);

            $labels = [];
            foreach (Company::$customLabels as $field) {
                $labels[$field] = $request->get("labels_{$field}");
            }
            $company->invoice_labels = json_encode($labels);
            $company->invoice_fields = $request->get('invoice_fields_json');

            $company->save();

            Session::flash('message', trans('texts.updated_settings'));
        }

        return Redirect::to('settings/' . ACCOUNT_INVOICE_DESIGN);
    }

    /**
     * @return RedirectResponse
     */
    private function saveCustomizeDesign()
    {
        $designId = intval($request->get('design_id')) ?: CUSTOM_DESIGN1;
        $field = 'custom_design' . ($designId - 10);

        if (Auth::user()->company->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN)) {
            $company = Auth::user()->company;
            if (! $company->custom_design1) {
                $company->invoice_design_id = CUSTOM_DESIGN1;
            }
            $company->$field = $request->get('custom_design');
            $company->save();

            Session::flash('message', trans('texts.updated_settings'));
        }

        return Redirect::to('settings/' . ACCOUNT_CUSTOMIZE_DESIGN . '?design_id=' . $designId);
    }

    /**
     * @return RedirectResponse
     */
    private function saveEmailTemplates()
    {
        if (Auth::user()->company->hasFeature(FEATURE_EMAIL_TEMPLATES_REMINDERS)) {
            $company = Auth::user()->company;

            foreach (AccountEmailSettings::$templates as $type) {
                $subjectField = "email_subject_{$type}";
                $subject = $request->get($subjectField, $company->getEmailSubject($type));
                $company->account_email_settings->$subjectField = ($subject == $company->getDefaultEmailSubject($type) ? null : $subject);

                $bodyField = "email_template_{$type}";
                $body = $request->get($bodyField, $company->getEmailTemplate($type));
                $company->account_email_settings->$bodyField = ($body == $company->getDefaultEmailTemplate($type) ? null : $body);
            }

            foreach ([TEMPLATE_REMINDER1, TEMPLATE_REMINDER2, TEMPLATE_REMINDER3, TEMPLATE_QUOTE_REMINDER1, TEMPLATE_QUOTE_REMINDER2, TEMPLATE_QUOTE_REMINDER3] as $type) {
                $enableField = "enable_{$type}";
                $company->account_email_settings->$enableField = $request->get($enableField) ? true : false;
                $company->account_email_settings->{"num_days_{$type}"} = $request->get("num_days_{$type}");
                $company->account_email_settings->{"field_{$type}"} = $request->get("field_{$type}");
                $company->account_email_settings->{"direction_{$type}"} = $request->get("field_{$type}") == REMINDER_FIELD_INVOICE_DATE ? REMINDER_DIRECTION_AFTER : $request->get("direction_{$type}");

                $number = preg_replace('/[^0-9]/', '', $type);
                if (strpos($type, 'quote') !== false) {
                    $company->account_email_settings->{"late_fee_quote{$number}_amount"} = $request->get("late_fee_quote{$number}_amount");
                    $company->account_email_settings->{"late_fee_quote{$number}_percent"} = $request->get("late_fee_quote{$number}_percent");
                } else {
                    $company->account_email_settings->{"late_fee{$number}_amount"} = $request->get("late_fee{$number}_amount");
                    $company->account_email_settings->{"late_fee{$number}_percent"} = $request->get("late_fee{$number}_percent");
                }
            }

            $company->account_email_settings->enable_reminder4 = $request->get('enable_reminder4') ? true : false;
            $company->account_email_settings->frequency_id_reminder4 = $request->get('frequency_id_reminder4');

            $company->account_email_settings->enable_quote_reminder4 = $request->get('enable_quote_reminder4') ? true : false;
            $company->account_email_settings->frequency_id_quote_reminder4 = $request->get('frequency_id_quote_reminder4');

            $company->save();
            $company->account_email_settings->save();

            Session::flash('message', trans('texts.updated_settings'));
        }

        return Redirect::to('settings/' . ACCOUNT_TEMPLATES_AND_REMINDERS);
    }

    /**
     * @return RedirectResponse
     */
    private function saveProducts()
    {
        $company = Auth::user()->company;

        $company->show_product_notes = $request->get('show_product_notes') ? true : false;
        $company->fill_products = $request->get('fill_products') ? true : false;
        $company->update_products = $request->get('update_products') ? true : false;
        $company->convert_products = $request->get('convert_products') ? true : false;
        $company->save();

        Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('settings/' . ACCOUNT_PRODUCTS);
    }

    /**
     * @return RedirectResponse
     */
    private function saveTaxRates()
    {
        $company = Auth::user()->company;
        $company->fill(\Request::all());
        $company->save();

        Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('settings/' . ACCOUNT_TAX_RATES);
    }

    /**
     * @return RedirectResponse
     */
    private function saveAccountManagement()
    {
        $user = Auth::user();
        $company = $user->company;
        $modules = $request->get('modules');

        if (Utils::isSelfHost()) {
            // get all custom modules, including disabled
            $custom_modules = collect($request->get('custom_modules'))->each(function ($item, $key): void {
                $module = Module::find($item);
                if (! $module) {
                    return;
                }
                if (! $module->disabled()) {
                    return;
                }
                $module->enable();
            });

            (Module::toCollection()->diff($custom_modules))->each(function ($item, $key): void {
                if ($item->enabled()) {
                    $item->disable();
                }
            });
        }

        $user->force_pdfjs = $request->get('force_pdfjs') ? true : false;
        $user->save();

        $company->live_preview = $request->get('live_preview') ? true : false;
        $company->realtime_preview = $request->get('realtime_preview') ? true : false;

        // Automatically disable live preview when using a large font
        $fonts = Cache::get('fonts')->filter(function ($font) use ($company): bool {
            if ($font->google_font) {
                return false;
            }

            return $font->id == $company->header_font_id || $font->id == $company->body_font_id;
        });
        if ($company->live_preview && $fonts->count()) {
            $company->live_preview = false;
            Session::flash('warning', trans('texts.live_preview_disabled'));
        }

        $company->enabled_modules = $modules ? array_sum($modules) : 0;
        $company->save();

        Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('settings/' . ACCOUNT_MANAGEMENT);
    }

    /**
     * @return RedirectResponse
     */
    public function saveClientPortalSettings(SaveClientPortalSettings $request)
    {
        $company = $request->user()->company;

        // check subdomain is unique in the lookup tables
        if (request()->subdomain) {
            if (! LookupAccount::validateField('subdomain', request()->subdomain, $company)) {
                return Redirect::to('settings/' . ACCOUNT_CLIENT_PORTAL)
                    ->withError(trans('texts.subdomain_taken'))
                    ->withInput();
            }
        }

        (bool) $fireUpdateSubdomainEvent = false;

        if ($company->subdomain !== $request->subdomain) {
            $fireUpdateSubdomainEvent = true;
            event(new SubdomainWasRemoved($company));
        }

        $company->fill($request->all());
        $company->client_view_css = $request->client_view_css;
        $company->client_view_js = $request->client_view_js;
        $company->subdomain = $request->subdomain;
        $company->iframe_url = $request->iframe_url;
        $company->is_custom_domain = $request->is_custom_domain;
        $company->save();

        if ($fireUpdateSubdomainEvent) {
            event(new SubdomainWasUpdated($company));
        }

        return redirect('settings/' . ACCOUNT_CLIENT_PORTAL)
            ->with('message', trans('texts.updated_settings'));
    }

    /**
     * @return $this|RedirectResponse
     */
    public function saveEmailSettings(SaveEmailSettings $request)
    {
        $company = $request->user()->company;
        $company->fill($request->all());
        $company->save();

        $settings = $company->account_email_settings;
        $settings->fill($request->all());
        $settings->save();

        return redirect('settings/' . ACCOUNT_EMAIL_SETTINGS)
            ->with('message', trans('texts.updated_settings'));
    }

    /**
     * @return RedirectResponse
     */
    public function saveTickets(SaveTicketSettings $request)
    {
        $company_ticket_settings = Auth::user()->company->company_ticket_settings;
        $company_ticket_settings->fill($request->all());
        $company_ticket_settings->save();

        Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('settings/' . ACCOUNT_TICKETS);
    }

    public function checkUniqueLocalPart()
    {
        if (AccountTicketSettings::checkUniqueLocalPart($request->get('support_email_local_part'), Auth::user()->company)) {
            return RESULT_SUCCESS;
        }

        return RESULT_FAILURE;
    }

    /**
     * @return RedirectResponse
     */
    public function updateDetails(UpdateAccountRequest $request)
    {
        $company = Auth::user()->company;
        $this->accountRepo->save($request->input(), $company);

        // Logo image file
        if ($uploaded = \Request::file('logo')) {
            $path = \Request::file('logo')->getRealPath();
            $disk = $company->getLogoDisk();
            $extension = strtolower($uploaded->getClientOriginalExtension());

            if (empty(Document::$types[$extension]) && ! empty(Document::$extraExtensions[$extension])) {
                $documentType = Document::$extraExtensions[$extension];
            } else {
                $documentType = $extension;
            }

            if (! in_array($documentType, ['jpeg', 'png', 'gif'])) {
                Session::flash('warning', 'Unsupported file type');
            } else {
                $documentTypeData = Document::$types[$documentType];

                $filePath = $uploaded->path();
                $size = filesize($filePath);

                if ($size / 1000 > MAX_DOCUMENT_SIZE) {
                    Session::flash('error', trans('texts.logo_warning_too_large'));
                } else {
                    if ($documentType != 'gif') {
                        $company->logo = $company->account_key . '.' . $documentType;

                        try {
                            $imageSize = getimagesize($filePath);
                            $company->logo_width = $imageSize[0];
                            $company->logo_height = $imageSize[1];
                            $company->logo_size = $size;

                            // make sure image isn't interlaced
                            if (extension_loaded('fileinfo')) {
                                $image = Image::make($path);
                                $image->interlace(false);
                                $imageStr = (string) $image->encode($documentType);
                                $disk->put($company->logo, $imageStr);
                                $company->logo_size = strlen($imageStr);
                            } else {
                                if (Utils::isInterlaced($filePath)) {
                                    $company->clearLogo();
                                    Session::flash('error', trans('texts.logo_warning_invalid'));
                                } else {
                                    $stream = fopen($filePath, 'r');
                                    $disk->getDriver()->putStream($company->logo, $stream, ['mimetype' => $documentTypeData['mime']]);
                                    fclose($stream);
                                }
                            }
                        } catch (Exception $exception) {
                            $company->clearLogo();
                            Session::flash('error', trans('texts.logo_warning_invalid'));
                        }
                    } else {
                        if (extension_loaded('fileinfo')) {
                            $company->logo = $company->account_key . '.png';
                            $image = Image::make($path);
                            $image = Image::canvas($image->width(), $image->height(), '#FFFFFF')->insert($image);
                            $imageStr = (string) $image->encode('png');
                            $disk->put($company->logo, $imageStr);

                            $company->logo_size = strlen($imageStr);
                            $company->logo_width = $image->width();
                            $company->logo_height = $image->height();
                        } else {
                            Session::flash('error', trans('texts.logo_warning_fileinfo'));
                        }
                    }
                }
            }

            $company->save();
        }

        event(new UserSettingsChanged());

        Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('settings/' . ACCOUNT_COMPANY_DETAILS);
    }

    /**
     * @return $this|RedirectResponse
     */
    public function saveUserDetails()
    {
        /** @var User $user */
        $user = Auth::user();
        $email = trim(strtolower($request->get('email')));

        if (! LookupUser::validateField('email', $email, $user)) {
            return Redirect::to('settings/' . ACCOUNT_USER_DETAILS)
                ->withError(trans('texts.email_taken'))
                ->withInput();
        }

        $rules = ['email' => 'email|required|unique:users,email,' . $user->id . ',id'];

        if ($user->google_2fa_secret) {
            $rules['phone'] = 'required';
        }

        $validator = Validator::make(\Request::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('settings/' . ACCOUNT_USER_DETAILS)
                ->withErrors($validator)
                ->withInput();
        }
        $user->first_name = trim($request->get('first_name'));
        $user->last_name = trim($request->get('last_name'));
        $user->username = $email;
        $user->email = $email;
        $user->phone = trim($request->get('phone'));
        $user->dark_mode = $request->get('dark_mode');
        $user->signature = $request->get('signature');


        $user->notify_sent = $request->get('notify_sent');
        $user->notify_viewed = $request->get('notify_viewed');
        $user->notify_paid = $request->get('notify_paid');
        $user->notify_approved = $request->get('notify_approved');
        $user->only_notify_owned = $request->get('only_notify_owned');

        if ($user->google_2fa_secret && ! $request->get('enable_two_factor')) {
            $user->google_2fa_secret = null;
        }

        if (Utils::isNinja()) {
            if ($request->get('referral_code') && ! $user->referral_code) {
                $user->referral_code = strtolower(str_random(RANDOM_KEY_LENGTH));
            }
        }

        $this->saveUserAvatar(\Request::file('avatar'), $user);

        $user->save();

        event(new UserSettingsChanged());
        Session::flash('message', trans('texts.updated_settings'));

        return Redirect::to('settings/' . ACCOUNT_USER_DETAILS);
    }

    private function saveUserAvatar($avatar, $user): void
    {
        // Logo image file
        if ($uploaded = $avatar) {
            $path = $avatar->getRealPath();
            $disk = $user->getAvatarDisk();
            $extension = strtolower($uploaded->getClientOriginalExtension());

            if (empty(Document::$types[$extension]) && ! empty(Document::$extraExtensions[$extension])) {
                $documentType = Document::$extraExtensions[$extension];
            } else {
                $documentType = $extension;
            }

            if (! in_array($documentType, ['jpeg', 'png', 'gif'])) {
                Session::flash('warning', 'Unsupported file type');
            } else {
                $documentTypeData = Document::$types[$documentType];

                $filePath = $uploaded->path();
                $size = filesize($filePath);

                if ($size / 1000 > MAX_DOCUMENT_SIZE) {
                    Session::flash('error', trans('texts.logo_warning_too_large'));
                } else {
                    if ($documentType != 'gif') {
                        $user->avatar = str_random(21) . '.' . $documentType;

                        try {
                            $imageSize = getimagesize($filePath);
                            $user->avatar_width = $imageSize[0];
                            $user->avatar_height = $imageSize[1];
                            $user->avatar_size = $size;

                            // make sure image isn't interlaced
                            if (extension_loaded('fileinfo')) {
                                $image = Image::make($path);
                                $image->interlace(false);
                                $imageStr = (string) $image->encode($documentType);
                                $disk->put($user->avatar, $imageStr);
                                $user->avatar_size = strlen($imageStr);
                            } else {
                                if (Utils::isInterlaced($filePath)) {
                                    $user->clearAvatar();
                                    Session::flash('error', trans('texts.logo_warning_invalid'));
                                } else {
                                    $stream = fopen($filePath, 'r');
                                    $disk->getDriver()->putStream($user->avatar, $stream, ['mimetype' => $documentTypeData['mime']]);
                                    fclose($stream);
                                }
                            }
                        } catch (Exception $exception) {
                            $user->clearAvatar();
                            Session::flash('error', trans('texts.logo_warning_invalid'));
                        }
                    } else {
                        if (extension_loaded('fileinfo')) {
                            $user->avatar = str_random(32) . '.png';
                            $image = Image::make($path);
                            $image = Image::canvas($image->width(), $image->height(), '#FFFFFF')->insert($image);
                            $imageStr = (string) $image->encode('png');
                            $disk->put($user->avatar, $imageStr);

                            $user->avatar_size = strlen($imageStr);
                            $user->avatar_width = $image->width();
                            $user->avatar_height = $image->height();
                        } else {
                            Session::flash('error', trans('texts.logo_warning_fileinfo'));
                        }
                    }
                }
            }

            $user->save();
        }
    }

    /**
     * @return RedirectResponse
     */
    public function removeLogo()
    {
        $company = Auth::user()->company;

        if (! Utils::isNinjaProd() && $company->hasLogo()) {
            $company->getLogoDisk()->delete($company->logo);
        }

        $company->logo = null;
        $company->logo_size = null;
        $company->logo_width = null;
        $company->logo_height = null;
        $company->save();

        Session::flash('message', trans('texts.removed_logo'));

        return Redirect::to('settings/' . ACCOUNT_COMPANY_DETAILS);
    }

    /**
     * @return mixed
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        if (! Utils::isNinjaProd() && $user->hasAvatar()) {
            $user->getAvatarDisk()->delete($user->avatar);
        }

        $user->avatar = null;
        $user->avatar_size = null;
        $user->avatar_width = null;
        $user->avatar_height = null;
        $user->save();

        Session::flash('message', trans('texts.removed_logo'));

        return Redirect::to('settings/' . ACCOUNT_USER_DETAILS);
    }

    public function checkEmail(): string
    {
        $email = trim(strtolower($request->get('email')));
        $user = Auth::user();

        if (! LookupUser::validateField('email', $email, $user)) {
            return 'taken';
        }

        $email = User::withTrashed()->where('email', '=', $email)
            ->where('id', '<>', $user->registered ? 0 : $user->id)
            ->first();

        if ($email) {
            return 'taken';
        }

        return 'available';
    }

    /**
     * @return string
     */
    public function submitSignup()
    {
        $user = Auth::user();
        $ip = request()->getClientIp();
        $company = $user->company;

        $rules = [
            'new_first_name' => 'required',
            'new_last_name'  => 'required',
            'new_password'   => 'required|min:6',
            'new_email'      => 'email|required|unique:users,email',
        ];

        if (! $user->registered) {
            $rules['new_email'] .= ',' . Auth::user()->id . ',id';
        }

        $validator = Validator::make(\Request::all(), $rules);

        if ($validator->fails()) {
            return '';
        }

        $firstName = trim($request->get('new_first_name'));
        $lastName = trim($request->get('new_last_name'));
        $email = trim(strtolower($request->get('new_email')));
        $password = trim($request->get('new_password'));

        if (! LookupUser::validateField('email', $email, $user)) {
            return '';
        }

        if ($user->registered) {
            $newAccount = $this->accountRepo->create($firstName, $lastName, $email, $password, $company->companyPlan);
            $newUser = $newAccount->users()->first();
            $newUser->acceptLatestTerms($ip)->save();
            $users = $this->accountRepo->associateAccounts($user->id, $newUser->id);

            Session::flash('message', trans('texts.created_new_company'));
            Session::put(SESSION_USER_ACCOUNTS, $users);
            Auth::loginUsingId($newUser->id);

            return RESULT_SUCCESS;
        }
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->email = $email;
        $user->username = $user->email;
        $user->password = bcrypt($password);
        $user->registered = true;
        $user->acceptLatestTerms($ip);
        $user->save();

        /*$user->company->startTrial(PLAN_PRO);

        if ($request->get('go_pro') == 'true') {
            session([REQUESTED_PRO_PLAN => true]);
        }*/

        return "{$user->first_name} {$user->last_name}";
    }

    /**
     * @return mixed
     */
    public function doRegister()
    {
        $affiliate = Affiliate::where('affiliate_key', '=', SELF_HOST_AFFILIATE_KEY)->first();
        $email = trim($request->get('email'));
        if (! $email) {
            return RESULT_FAILURE;
        }
        if ($email == TEST_USERNAME) {
            return RESULT_FAILURE;
        }

        $license = new License();
        $license->first_name = $request->get('first_name');
        $license->last_name = $request->get('last_name');
        $license->email = $email;
        $license->transaction_reference = request()->getClientIp();
        $license->license_key = Utils::generateLicense();
        $license->affiliate_id = $affiliate->id;
        $license->product_id = PRODUCT_SELF_HOST;
        $license->is_claimed = 1;
        $license->save();

        return RESULT_SUCCESS;
    }

    /**
     * @return RedirectResponse
     */
    public function purgeData()
    {
        $this->dispatch(new PurgeAccountData());

        return redirect('/settings/account_management')->withMessage(trans('texts.purge_successful'));
    }

    /**
     * @return RedirectResponse
     */
    public function cancelAccount()
    {
        if ($reason = trim($request->get('reason'))) {
            $email = Auth::user()->email;
            $name = Auth::user()->getDisplayName();

            $data = [
                'text' => $reason,
            ];

            $subject = 'Invoice Ninja - Canceled company';

            $this->userMailer->sendTo(env('CONTACT_EMAIL', CONTACT_EMAIL), $email, $name, $subject, 'contact', $data);
        }

        $user = Auth::user();
        $company = Auth::user()->company;

        Log::info("Canceled company: {$company->name} - {$user->email}");
        $type = $company->hasMultipleAccounts() ? 'companyPlan' : 'company';
        $subject = trans("texts.deleted_{$type}");
        $message = trans("texts.deleted_{$type}_details", ['company' => $company->getDisplayName()]);
        $this->userMailer->sendMessage($user, $subject, $message);

        $refunded = false;
        if (! $company->hasMultipleAccounts()) {
            $companyPlan = $company->companyPlan;
            $refunded = $companyPlan->processRefund(Auth::user());

            $ninjaClient = $this->accountRepo->getNinjaClient($company);
            dispatch(new PurgeClientData($ninjaClient));
        }

        Document::scope()->each(function ($item, $key): void {
            $item->delete();
        });

        $this->accountRepo->unlinkAccount($company);
        $company->forceDelete();

        Auth::logout();
        Session::flush();

        if ($refunded) {
            Session::flash('warning', trans('texts.plan_refunded'));
        }

        return Redirect::to('/')->with('clearGuestKey', true);
    }

    /**
     * @return RedirectResponse
     */
    public function resendConfirmation()
    {
        /** @var User $user */
        $user = Auth::user();
        $this->userMailer->sendConfirmation($user);

        return Redirect::to('/settings/' . ACCOUNT_USER_DETAILS)->with('message', trans('texts.confirmation_resent'));
    }

    /**
     * @param bool $subSection
     *
     * @return RedirectResponse
     */
    public function redirectLegacy($section, $subSection = false)
    {
        if ($section === 'details') {
            $section = ACCOUNT_COMPANY_DETAILS;
        } elseif ($section === 'payments') {
            $section = ACCOUNT_PAYMENTS;
        } elseif ($section === 'advanced_settings') {
            $section = $subSection;
            if ($section === 'token_management') {
                $section = ACCOUNT_API_TOKENS;
            }
        }

        if (! in_array($section, array_merge(Company::$basicSettings, Company::$advancedSettings))) {
            $section = ACCOUNT_COMPANY_DETAILS;
        }

        return Redirect::to("/settings/$section/", 301);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function previewEmail(TemplateService $templateService)
    {
        $template = $request->get('template');
        $invitation = Invitation::scope()
            ->with('invoice.client.contacts')
            ->first();

        if (! $invitation) {
            return trans('texts.create_invoice_for_sample');
        }

        /** @var company $company */
        $company = Auth::user()->company;
        $invoice = $invitation->invoice;

        // replace the variables with sample data
        $data = [
            'company'    => $company,
            'invoice'    => $invoice,
            'invitation' => $invitation,
            'link'       => $invitation->getLink(),
            'client'     => $invoice->client,
            'amount'     => $invoice->amount,
        ];

        // create the email view
        $view = 'emails.' . $company->getTemplateView(ENTITY_INVOICE) . '_html';
        $data = array_merge($data, [
            'body'       => $templateService->processVariables($template, $data),
            'entityType' => ENTITY_INVOICE,
        ]);

        return Response::view($view, $data);
    }
}
