<?php

namespace App\Http\Controllers;

use App\Events\SubdomainWasRemoved;
use App\Events\SubdomainWasUpdated;
use App\Events\UserSettingsChanged;
use App\Events\UserSignedUp;
use App\Http\Requests\SaveClientPortalSettings;
use App\Http\Requests\SaveEmailSettings;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Models\AccountEmailSettings;
use App\Models\AccountGateway;
use App\Models\Affiliate;
use App\Models\Document;
use App\Models\Gateway;
use App\Models\GatewayType;
use App\Models\Invoice;
use App\Models\InvoiceDesign;
use App\Models\License;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\User;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Repositories\ReferralRepository;
use App\Services\AuthService;
use App\Services\PaymentService;
use App\Services\TemplateService;
use Exception;
use File;
use Illuminate\Http\RedirectResponse;
use Image;
use Nwidart\Modules\Facades\Module;
use stdClass;
use Utils;
use View;

/**
 * Class AccountController.
 */
class AccountController extends BaseController
{
    protected \App\Ninja\Repositories\AccountRepository $accountRepo;

    protected \App\Ninja\Mailers\UserMailer $userMailer;

    protected \App\Ninja\Mailers\ContactMailer $contactMailer;

    protected \App\Ninja\Repositories\ReferralRepository $referralRepository;

    protected \App\Services\PaymentService $paymentService;

    /**
     * AccountController constructor.
     *
     * @param AccountRepository  $accountRepo
     * @param UserMailer         $userMailer
     * @param ContactMailer      $contactMailer
     * @param ReferralRepository $referralRepository
     * @param PaymentService     $paymentService
     */
    public function __construct(
        AccountRepository $accountRepo,
        UserMailer $userMailer,
        ContactMailer $contactMailer,
        ReferralRepository $referralRepository,
        PaymentService $paymentService
    ) {
        $this->accountRepo = $accountRepo;
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
        $account = false;
        $guestKey = \Illuminate\Support\Facades\Request::input('guest_key'); // local storage key to login until registered

        if (\Illuminate\Support\Facades\Auth::check()) {
            return \Illuminate\Support\Facades\Redirect::to('invoices/create');
        }

        if ( ! Utils::isNinja() && Account::count() > 0) {
            return \Illuminate\Support\Facades\Redirect::to('/login');
        }

        if ($guestKey) {
            $user = User::where('password', '=', $guestKey)->first();

            if ($user && $user->registered) {
                return \Illuminate\Support\Facades\Redirect::to('/');
            }
        }

        if ( ! $user) {
            $account = $this->accountRepo->create();
            $user = $account->users()->first();
        }

        \Illuminate\Support\Facades\Auth::login($user, true);
        event(new UserSignedUp());

        if ($account && $account->language_id && $account->language_id != DEFAULT_LANGUAGE) {
            $link = link_to('/invoices/create?lang=en', 'click here');
            $message = sprintf('Your account language has been set automatically, %s to change to English', $link);
            \Illuminate\Support\Facades\Session::flash('warning', $message);
        }

        if ($redirectTo = \Illuminate\Support\Facades\Request::input('redirect_to')) {
            $redirectTo = SITE_URL . '/' . ltrim($redirectTo, '/');
        } else {
            $redirectTo = \Illuminate\Support\Facades\Request::input('sign_up') ? 'dashboard' : 'invoices/create';
        }

        return \Illuminate\Support\Facades\Redirect::to($redirectTo)->with('sign_up', \Illuminate\Support\Facades\Request::input('sign_up'));
    }

    /**
     * @return RedirectResponse
     */
    public function changePlan()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $account = $user->account;
        $company = $account->company;

        $plan = \Illuminate\Support\Facades\Request::input('plan');
        $term = \Illuminate\Support\Facades\Request::input('plan_term');
        $numUsers = \Illuminate\Support\Facades\Request::input('num_users');

        if ($plan != PLAN_ENTERPRISE) {
            $numUsers = 1;
        }

        $planDetails = $account->getPlanDetails(false, false);

        $newPlan = [
            'plan'      => $plan,
            'term'      => $term,
            'num_users' => $numUsers,
        ];
        $newPlan['price'] = Utils::getPlanPrice($newPlan);
        $credit = 0;

        if ($plan == PLAN_FREE && $company->processRefund(\Illuminate\Support\Facades\Auth::user())) {
            \Illuminate\Support\Facades\Session::flash('warning', trans('texts.plan_refunded'));
        }

        if ($company->payment && ! empty($planDetails['paid']) && $plan != PLAN_FREE) {
            $time_used = $planDetails['paid']->diff(date_create());
            $days_used = $time_used->days;

            if ($time_used->invert) {
                // They paid in advance
                $days_used *= -1;
            }

            $days_total = $planDetails['paid']->diff($planDetails['expires'])->days;
            $percent_used = $days_used / $days_total;
            $credit = round((float) ($company->payment->amount) * (1 - $percent_used), 2);
        }

        if ($newPlan['price'] > $credit) {
            $invitation = $this->accountRepo->enablePlan($newPlan, $credit);

            return \Illuminate\Support\Facades\Redirect::to('view/' . $invitation->invitation_key);
        }

        if ($plan == PLAN_FREE) {
            $company->discount = 0;

            $ninjaClient = $this->accountRepo->getNinjaClient($account);
            $ninjaClient->send_reminders = false;
            $ninjaClient->save();
        } else {
            $company->plan_term = $term;
            $company->plan_price = $newPlan['price'];
            $company->num_users = $numUsers;
            $company->plan_expires = date_create()->modify($term == PLAN_TERM_MONTHLY ? '+1 month' : '+1 year')->format('Y-m-d');
        }

        $company->trial_plan = null;
        $company->plan = $plan;
        $company->save();

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_plan'));

        return \Illuminate\Support\Facades\Redirect::to('settings/account_management');
    }

    /**
     * @param       $entityType
     * @param       $visible
     * @param mixed $filter
     *
     * @return mixed
     */
    public function setEntityFilter($entityType, $filter = ''): string
    {
        if ($filter == 'true') {
            $filter = '';
        }

        // separate state and status filters
        $filters = explode(',', $filter);
        $stateFilter = [];
        $statusFilter = [];
        foreach ($filters as $filter) {
            if (in_array($filter, \App\Models\EntityModel::$statuses)) {
                $stateFilter[] = $filter;
            } else {
                $statusFilter[] = $filter;
            }
        }

        \Illuminate\Support\Facades\Session::put('entity_state_filter:' . $entityType, implode(',', $stateFilter));
        \Illuminate\Support\Facades\Session::put('entity_status_filter:' . $entityType, implode(',', $statusFilter));

        return RESULT_SUCCESS;
    }

    public function getSearchData(): \Illuminate\Http\JsonResponse
    {
        $data = $this->accountRepo->getSearchData(\Illuminate\Support\Facades\Auth::user());

        return \Illuminate\Support\Facades\Response::json($data);
    }

    public function showSection($section = false): \Illuminate\Contracts\View\View|RedirectResponse
    {
        if ( ! \Illuminate\Support\Facades\Auth::user()->is_admin) {
            return \Illuminate\Support\Facades\Redirect::to('/settings/user_details');
        }

        if ( ! $section) {
            return \Illuminate\Support\Facades\Redirect::to('/settings/' . ACCOUNT_COMPANY_DETAILS, 301);
        }

        if ($section == ACCOUNT_COMPANY_DETAILS) {
            return self::showCompanyDetails();
        }

        if ($section == ACCOUNT_LOCALIZATION) {
            return self::showLocalization();
        }

        if ($section == ACCOUNT_PAYMENTS) {
            return self::showOnlinePayments();
        }

        if ($section == ACCOUNT_BANKS) {
            return self::showBankAccounts();
        }

        if ($section == ACCOUNT_INVOICE_SETTINGS) {
            return self::showInvoiceSettings();
        }

        if ($section == ACCOUNT_IMPORT_EXPORT) {
            return \Illuminate\Support\Facades\View::make('accounts.import_export', ['title' => trans('texts.import_export')]);
        }

        if ($section == ACCOUNT_MANAGEMENT) {
            return self::showAccountManagement();
        }

        if ($section == ACCOUNT_INVOICE_DESIGN || $section == ACCOUNT_CUSTOMIZE_DESIGN) {
            return self::showInvoiceDesign($section);
        }

        if ($section == ACCOUNT_CLIENT_PORTAL) {
            return self::showClientPortal();
        }

        if ($section === ACCOUNT_TEMPLATES_AND_REMINDERS) {
            return self::showTemplates();
        }

        if ($section === ACCOUNT_PRODUCTS) {
            return self::showProducts();
        }

        if ($section === ACCOUNT_TAX_RATES) {
            return self::showTaxRates();
        }

        if ($section === ACCOUNT_PAYMENT_TERMS) {
            return self::showPaymentTerms();
        }

        if ($section === ACCOUNT_SYSTEM_SETTINGS) {
            return self::showSystemSettings();
        }

        $view = 'accounts.' . $section;
        if ( ! view()->exists($view)) {
            return redirect('/settings/company_details');
        }

        $data = [
            'account' => Account::with('users')->findOrFail(\Illuminate\Support\Facades\Auth::user()->account_id),
            'title'   => trans('texts.' . $section),
            'section' => $section,
        ];

        return \Illuminate\Support\Facades\View::make($view, $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function showUserDetails(): \Illuminate\Contracts\View\View
    {
        if ( ! auth()->user()->registered) {
            return redirect('/')->withError(trans('texts.sign_up_to_save'));
        }

        $oauthLoginUrls = [];
        foreach (AuthService::$providers as $provider) {
            $oauthLoginUrls[] = ['label' => $provider, 'url' => \Illuminate\Support\Facades\URL::to('/auth/' . mb_strtolower($provider))];
        }

        $data = [
            'account'           => Account::with('users')->findOrFail(\Illuminate\Support\Facades\Auth::user()->account_id),
            'title'             => trans('texts.user_details'),
            'user'              => \Illuminate\Support\Facades\Auth::user(),
            'oauthProviderName' => AuthService::getProviderName(\Illuminate\Support\Facades\Auth::user()->oauth_provider_id),
            'oauthLoginUrls'    => $oauthLoginUrls,
            'referralCounts'    => $this->referralRepository->getCounts(\Illuminate\Support\Facades\Auth::user()->referral_code),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.user_details', $data);
    }

    public function doSection($section): RedirectResponse
    {
        if ($section === ACCOUNT_LOCALIZATION) {
            return self::saveLocalization();
        }

        if ($section == ACCOUNT_PAYMENTS) {
            return self::saveOnlinePayments();
        }

        if ($section === ACCOUNT_NOTIFICATIONS) {
            return self::saveNotifications();
        }

        if ($section === ACCOUNT_EXPORT) {
            return self::export();
        }

        if ($section === ACCOUNT_INVOICE_SETTINGS) {
            return self::saveInvoiceSettings();
        }

        if ($section === ACCOUNT_INVOICE_DESIGN) {
            return self::saveInvoiceDesign();
        }

        if ($section === ACCOUNT_CUSTOMIZE_DESIGN) {
            return self::saveCustomizeDesign();
        }

        if ($section === ACCOUNT_TEMPLATES_AND_REMINDERS) {
            return self::saveEmailTemplates();
        }

        if ($section === ACCOUNT_PRODUCTS) {
            return self::saveProducts();
        }

        if ($section === ACCOUNT_TAX_RATES) {
            return self::saveTaxRates();
        }

        if ($section === ACCOUNT_PAYMENT_TERMS) {
            return self::savePaymentTerms();
        }

        if ($section === ACCOUNT_MANAGEMENT) {
            return self::saveAccountManagement();
        }
    }

    /**
     * @return RedirectResponse
     */
    public function saveClientPortalSettings(SaveClientPortalSettings $request)
    {
        $account = $request->user()->account;

        // check subdomain is unique in the lookup tables
        if (request()->subdomain && ! \App\Models\LookupAccount::validateField('subdomain', request()->subdomain, $account)) {
            return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_CLIENT_PORTAL)
                ->withError(trans('texts.subdomain_taken'))
                ->withInput();
        }

        (bool) $fireUpdateSubdomainEvent = false;

        if ($account->subdomain !== $request->subdomain) {
            $fireUpdateSubdomainEvent = true;
            event(new SubdomainWasRemoved($account));
        }

        $account->fill($request->all());
        $account->client_view_css = $request->client_view_css;
        $account->subdomain = $request->subdomain;
        $account->iframe_url = $request->iframe_url;
        $account->is_custom_domain = $request->is_custom_domain;
        $account->save();

        if ($fireUpdateSubdomainEvent) {
            event(new SubdomainWasUpdated($account));
        }

        return redirect('settings/' . ACCOUNT_CLIENT_PORTAL)
            ->with('message', trans('texts.updated_settings'));
    }

    /**
     * @return $this|RedirectResponse
     */
    public function saveEmailSettings(SaveEmailSettings $request)
    {
        $account = $request->user()->account;
        $account->fill($request->all());
        $account->save();

        $settings = $account->account_email_settings;
        $settings->fill($request->all());
        $settings->save();

        return redirect('settings/' . ACCOUNT_EMAIL_SETTINGS)
            ->with('message', trans('texts.updated_settings'));
    }

    /**
     * @param UpdateAccountRequest $request
     *
     * @return RedirectResponse
     */
    public function updateDetails(UpdateAccountRequest $request)
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $this->accountRepo->save($request->input(), $account);

        // Logo image file
        if ($uploaded = \Illuminate\Support\Facades\Request::file('logo')) {
            $path = \Illuminate\Support\Facades\Request::file('logo')->getRealPath();
            $disk = $account->getLogoDisk();
            $extension = mb_strtolower($uploaded->getClientOriginalExtension());

            if (empty(Document::$types[$extension]) && ! empty(Document::$extraExtensions[$extension])) {
                $documentType = Document::$extraExtensions[$extension];
            } else {
                $documentType = $extension;
            }

            if ( ! in_array($documentType, ['jpeg', 'png', 'gif'])) {
                \Illuminate\Support\Facades\Session::flash('warning', 'Unsupported file type');
            } else {
                $documentTypeData = Document::$types[$documentType];

                $filePath = $uploaded->path();
                $size = filesize($filePath);

                if ($size / 1000 > MAX_DOCUMENT_SIZE) {
                    \Illuminate\Support\Facades\Session::flash('error', trans('texts.logo_warning_too_large'));
                } elseif ($documentType != 'gif') {
                    $account->logo = $account->account_key . '.' . $documentType;
                    try {
                        $imageSize = getimagesize($filePath);
                        $account->logo_width = $imageSize[0];
                        $account->logo_height = $imageSize[1];
                        $account->logo_size = $size;

                        // make sure image isn't interlaced
                        if (extension_loaded('fileinfo')) {
                            $image = Image::make($path);
                            $image->interlace(false);
                            $imageStr = (string) $image->encode($documentType);
                            $disk->put($account->logo, $imageStr);
                            $account->logo_size = mb_strlen($imageStr);
                        } elseif (Utils::isInterlaced($filePath)) {
                            $account->clearLogo();
                            \Illuminate\Support\Facades\Session::flash('error', trans('texts.logo_warning_invalid'));
                        } else {
                            $stream = fopen($filePath, 'r');
                            $disk->getDriver()->putStream($account->logo, $stream, ['mimetype' => $documentTypeData['mime']]);
                            fclose($stream);
                        }
                    } catch (Exception) {
                        $account->clearLogo();
                        \Illuminate\Support\Facades\Session::flash('error', trans('texts.logo_warning_invalid'));
                    }
                } elseif (extension_loaded('fileinfo')) {
                    $account->logo = $account->account_key . '.png';
                    $image = Image::make($path);
                    $image = Image::canvas($image->width(), $image->height(), '#FFFFFF')->insert($image);
                    $imageStr = (string) $image->encode('png');
                    $disk->put($account->logo, $imageStr);
                    $account->logo_size = mb_strlen($imageStr);
                    $account->logo_width = $image->width();
                    $account->logo_height = $image->height();
                } else {
                    \Illuminate\Support\Facades\Session::flash('error', trans('texts.logo_warning_fileinfo'));
                }
            }

            $account->save();
        }

        event(new UserSettingsChanged());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_COMPANY_DETAILS);
    }

    /**
     * @return $this|RedirectResponse
     */
    public function saveUserDetails()
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $email = trim(mb_strtolower(\Illuminate\Support\Facades\Request::input('email')));

        if ( ! \App\Models\LookupUser::validateField('email', $email, $user)) {
            return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_DETAILS)
                ->withError(trans('texts.email_taken'))
                ->withInput();
        }

        $rules = ['email' => 'email|required|unique:users,email,' . $user->id . ',id'];

        if ($user->google_2fa_secret) {
            $rules['phone'] = 'required';
        }

        $validator = \Illuminate\Support\Facades\Validator::make(\Illuminate\Support\Facades\Request::all(), $rules);

        if ($validator->fails()) {
            return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_DETAILS)
                ->withErrors($validator)
                ->withInput();
        }

        $user->first_name = trim(\Illuminate\Support\Facades\Request::input('first_name'));
        $user->last_name = trim(\Illuminate\Support\Facades\Request::input('last_name'));
        $user->username = $email;
        $user->email = $email;
        $user->phone = trim(\Illuminate\Support\Facades\Request::input('phone'));
        $user->dark_mode = \Illuminate\Support\Facades\Request::input('dark_mode');

        if ( ! \Illuminate\Support\Facades\Auth::user()->is_admin) {
            $user->notify_sent = \Illuminate\Support\Facades\Request::input('notify_sent');
            $user->notify_viewed = \Illuminate\Support\Facades\Request::input('notify_viewed');
            $user->notify_paid = \Illuminate\Support\Facades\Request::input('notify_paid');
            $user->notify_approved = \Illuminate\Support\Facades\Request::input('notify_approved');
            $user->only_notify_owned = \Illuminate\Support\Facades\Request::input('only_notify_owned');
        }

        if ($user->google_2fa_secret && ! \Illuminate\Support\Facades\Request::input('enable_two_factor')) {
            $user->google_2fa_secret = null;
        }

        if (Utils::isNinja() && (\Illuminate\Support\Facades\Request::input('referral_code') && ! $user->referral_code)) {
            $user->referral_code = mb_strtolower(\Illuminate\Support\Str::random(RANDOM_KEY_LENGTH));
        }

        $user->save();

        event(new UserSettingsChanged());
        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_DETAILS);
    }

    /**
     * @return RedirectResponse
     */
    public function removeLogo()
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;

        if ( ! Utils::isNinjaProd() && $account->hasLogo()) {
            $account->getLogoDisk()->delete($account->logo);
        }

        $account->logo = null;
        $account->logo_size = null;
        $account->logo_width = null;
        $account->logo_height = null;
        $account->save();

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.removed_logo'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_COMPANY_DETAILS);
    }

    public function checkEmail(): string
    {
        $email = trim(mb_strtolower(\Illuminate\Support\Facades\Request::input('email')));
        $user = \Illuminate\Support\Facades\Auth::user();

        if ( ! \App\Models\LookupUser::validateField('email', $email, $user)) {
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

    public function submitSignup(): string
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $ip = \Illuminate\Support\Facades\Request::getClientIp();
        $account = $user->account;

        $rules = [
            'new_first_name' => 'required',
            'new_last_name'  => 'required',
            'new_password'   => 'required|min:6',
            'new_email'      => 'email|required|unique:users,email',
        ];

        if ( ! $user->registered) {
            $rules['new_email'] .= ',' . \Illuminate\Support\Facades\Auth::user()->id . ',id';
        }

        $validator = \Illuminate\Support\Facades\Validator::make(\Illuminate\Support\Facades\Request::all(), $rules);

        if ($validator->fails()) {
            return '';
        }

        $firstName = trim(\Illuminate\Support\Facades\Request::input('new_first_name'));
        $lastName = trim(\Illuminate\Support\Facades\Request::input('new_last_name'));
        $email = trim(mb_strtolower(\Illuminate\Support\Facades\Request::input('new_email')));
        $password = trim(\Illuminate\Support\Facades\Request::input('new_password'));

        if ( ! \App\Models\LookupUser::validateField('email', $email, $user)) {
            return '';
        }

        if ($user->registered) {
            $newAccount = $this->accountRepo->create($firstName, $lastName, $email, $password, $account->company);
            $newUser = $newAccount->users()->first();
            $newUser->acceptLatestTerms($ip)->save();
            $users = $this->accountRepo->associateAccounts($user->id, $newUser->id);

            \Illuminate\Support\Facades\Session::flash('message', trans('texts.created_new_company'));
            \Illuminate\Support\Facades\Session::put(SESSION_USER_ACCOUNTS, $users);
            \Illuminate\Support\Facades\Auth::loginUsingId($newUser->id);

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

        $user->account->startTrial(PLAN_PRO);

        if (\Illuminate\Support\Facades\Request::input('go_pro') == 'true') {
            session([REQUESTED_PRO_PLAN => true]);
        }

        return sprintf('%s %s', $user->first_name, $user->last_name);
    }

    public function doRegister(): string
    {
        $affiliate = Affiliate::where('affiliate_key', '=', SELF_HOST_AFFILIATE_KEY)->first();
        $email = trim(\Illuminate\Support\Facades\Request::input('email'));

        if ( ! $email || $email == TEST_USERNAME) {
            return RESULT_FAILURE;
        }

        $license = new License();
        $license->first_name = \Illuminate\Support\Facades\Request::input('first_name');
        $license->last_name = \Illuminate\Support\Facades\Request::input('last_name');
        $license->email = $email;
        $license->transaction_reference = \Illuminate\Support\Facades\Request::getClientIp();
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
        $this->dispatch(new \App\Jobs\PurgeAccountData());

        return redirect('/settings/account_management')->withMessage(trans('texts.purge_successful'));
    }

    /**
     * @return RedirectResponse
     */
    public function cancelAccount()
    {
        if (($reason = trim(\Illuminate\Support\Facades\Request::input('reason'))) !== '' && ($reason = trim(\Illuminate\Support\Facades\Request::input('reason'))) !== '0') {
            $email = \Illuminate\Support\Facades\Auth::user()->email;
            $name = \Illuminate\Support\Facades\Auth::user()->getDisplayName();

            $data = [
                'text' => $reason,
            ];

            $subject = 'Invoice Ninja - Canceled Account';

            $this->userMailer->sendTo(env('CONTACT_EMAIL', CONTACT_EMAIL), $email, $name, $subject, 'contact', $data);
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $account = \Illuminate\Support\Facades\Auth::user()->account;

        \Illuminate\Support\Facades\Log::info(sprintf('Canceled Account: %s - %s', $account->name, $user->email));
        $type = $account->hasMultipleAccounts() ? 'company' : 'account';
        $subject = trans('texts.deleted_' . $type);
        $message = trans(sprintf('texts.deleted_%s_details', $type), ['account' => $account->getDisplayName()]);
        $this->userMailer->sendMessage($user, $subject, $message);

        $refunded = false;
        if ( ! $account->hasMultipleAccounts()) {
            $company = $account->company;
            $refunded = $company->processRefund(\Illuminate\Support\Facades\Auth::user());

            $ninjaClient = $this->accountRepo->getNinjaClient($account);
            dispatch_sync(new \App\Jobs\PurgeClientData($ninjaClient));
        }

        Document::scope()->each(function ($item, $key): void {
            $item->delete();
        });

        $this->accountRepo->unlinkAccount($account);
        $account->forceDelete();

        \Illuminate\Support\Facades\Auth::logout();
        \Illuminate\Support\Facades\Session::flush();

        if ($refunded) {
            \Illuminate\Support\Facades\Session::flash('warning', trans('texts.plan_refunded'));
        }

        return \Illuminate\Support\Facades\Redirect::to('/')->with('clearGuestKey', true);
    }

    /**
     * @return RedirectResponse
     */
    public function resendConfirmation()
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $this->userMailer->sendConfirmation($user);

        return \Illuminate\Support\Facades\Redirect::to('/settings/' . ACCOUNT_USER_DETAILS)->with('message', trans('texts.confirmation_resent'));
    }

    /**
     * @param      $section
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

        if ( ! in_array($section, array_merge(Account::$basicSettings, Account::$advancedSettings))) {
            $section = ACCOUNT_COMPANY_DETAILS;
        }

        return \Illuminate\Support\Facades\Redirect::to(sprintf('/settings/%s/', $section), 301);
    }

    /**
     * @param TemplateService $templateService
     *
     * @return \Illuminate\Http\Response
     */
    public function previewEmail(TemplateService $templateService)
    {
        $template = \Illuminate\Support\Facades\Request::input('template');
        $invitation = \App\Models\Invitation::scope()
            ->with('invoice.client.contacts')
            ->first();

        if ( ! $invitation) {
            return trans('texts.create_invoice_for_sample');
        }

        /** @var \App\Models\Account $account */
        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $invoice = $invitation->invoice;

        // replace the variables with sample data
        $data = [
            'account'    => $account,
            'invoice'    => $invoice,
            'invitation' => $invitation,
            'link'       => $invitation->getLink(),
            'client'     => $invoice->client,
            'amount'     => $invoice->amount,
        ];

        // create the email view
        $view = 'emails.' . $account->getTemplateView(ENTITY_INVOICE) . '_html';
        $data = array_merge($data, [
            'body'       => $templateService->processVariables($template, $data),
            'entityType' => ENTITY_INVOICE,
        ]);

        return \Illuminate\Support\Facades\Response::view($view, $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View|RedirectResponse
     */
    private function showSystemSettings()
    {
        if (Utils::isNinjaProd()) {
            return \Illuminate\Support\Facades\Redirect::to('/');
        }

        $data = [
            'account' => Account::with('users')->findOrFail(\Illuminate\Support\Facades\Auth::user()->account_id),
            'title'   => trans('texts.system_settings'),
            'section' => ACCOUNT_SYSTEM_SETTINGS,
        ];

        return \Illuminate\Support\Facades\View::make('accounts.system_settings', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showInvoiceSettings()
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $recurringHours = [];

        for ($i = 0; $i < 24; $i++) {
            $format = $account->military_time ? 'H:i' : 'g:i a';
            $recurringHours[$i] = date($format, strtotime($i . ':00'));
        }

        $data = [
            'account'        => Account::with('users')->findOrFail(\Illuminate\Support\Facades\Auth::user()->account_id),
            'title'          => trans('texts.invoice_settings'),
            'section'        => ACCOUNT_INVOICE_SETTINGS,
            'recurringHours' => $recurringHours,
        ];

        return \Illuminate\Support\Facades\View::make('accounts.invoice_settings', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showCompanyDetails()
    {
        // check that logo is less than the max file size
        $account = \Illuminate\Support\Facades\Auth::user()->account;
        if ($account->isLogoTooLarge()) {
            \Illuminate\Support\Facades\Session::flash('warning', trans('texts.logo_too_large', ['size' => $account->getLogoSize() . 'KB']));
        }

        $data = [
            'account' => Account::with('users')->findOrFail(\Illuminate\Support\Facades\Auth::user()->account_id),
            'sizes'   => \Illuminate\Support\Facades\Cache::get('sizes'),
            'title'   => trans('texts.company_details'),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.details', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showAccountManagement()
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $planDetails = $account->getPlanDetails(true, false);
        $portalLink = false;

        if (Utils::isNinja() && $planDetails
            && $account->getPrimaryAccount()->id == auth()->user()->account_id
            && $ninjaClient = $this->accountRepo->getNinjaClient($account)) {
            $contact = $ninjaClient->getPrimaryContact();
            $portalLink = $contact->link;
        }

        $data = [
            'account'     => $account,
            'portalLink'  => $portalLink,
            'planDetails' => $planDetails,
            'title'       => trans('texts.account_management'),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.management', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showLocalization()
    {
        $data = [
            'account'         => Account::with('users')->findOrFail(\Illuminate\Support\Facades\Auth::user()->account_id),
            'timezones'       => \Illuminate\Support\Facades\Cache::get('timezones'),
            'dateFormats'     => \Illuminate\Support\Facades\Cache::get('dateFormats'),
            'datetimeFormats' => \Illuminate\Support\Facades\Cache::get('datetimeFormats'),
            'title'           => trans('texts.localization'),
            'weekdays'        => Utils::getTranslatedWeekdayNames(),
            'months'          => Utils::getMonthOptions(),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.localization', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showBankAccounts()
    {
        $account = auth()->user()->account;

        return \Illuminate\Support\Facades\View::make('accounts.banks', [
            'title'              => trans('texts.bank_accounts'),
            'advanced'           => ! \Illuminate\Support\Facades\Auth::user()->hasFeature(FEATURE_EXPENSES),
            'warnPaymentGateway' => ! $account->account_gateways->count(),
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\View|RedirectResponse
     */
    private function showOnlinePayments()
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $account->load('account_gateways');

        $count = $account->account_gateways->count();
        $trashedCount = AccountGateway::scope()->withTrashed()->count();

        if (($accountGateway = $account->getGatewayConfig(GATEWAY_STRIPE)) && ! $accountGateway->getPublishableKey()) {
            \Illuminate\Support\Facades\Session::now('warning', trans('texts.missing_publishable_key'));
        }

        $tokenBillingOptions = [];
        for ($i = 1; $i <= 4; $i++) {
            $tokenBillingOptions[$i] = trans('texts.token_billing_' . $i);
        }

        return \Illuminate\Support\Facades\View::make('accounts.payments', [
            'showAdd'             => $count < count(Gateway::$alternate) + 1,
            'title'               => trans('texts.online_payments'),
            'tokenBillingOptions' => $tokenBillingOptions,
            'currency'            => Utils::getFromCache(\Illuminate\Support\Facades\Session::get(SESSION_CURRENCY, DEFAULT_CURRENCY), 'currencies'),
            'taxRates'            => TaxRate::scope()->whereIsInclusive(false)->orderBy('rate')->get(['public_id', 'name', 'rate']),
            'account'             => $account,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showProducts()
    {
        $data = [
            'account' => \Illuminate\Support\Facades\Auth::user()->account,
            'title'   => trans('texts.product_library'),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.products', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showTaxRates()
    {
        $data = [
            'account'              => \Illuminate\Support\Facades\Auth::user()->account,
            'title'                => trans('texts.tax_rates'),
            'taxRates'             => TaxRate::scope()->whereIsInclusive(false)->get(),
            'countInvoices'        => Invoice::scope()->withTrashed()->count(),
            'hasInclusiveTaxRates' => (bool) TaxRate::scope()->whereIsInclusive(true)->count(),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.tax_rates', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showPaymentTerms()
    {
        $data = [
            'account' => \Illuminate\Support\Facades\Auth::user()->account,
            'title'   => trans('texts.payment_terms'),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.payment_terms', $data);
    }

    /**
     * @param $section
     *
     * @return \Illuminate\Contracts\View\View
     */
    private function showInvoiceDesign($section)
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account->load('country');

        if ($invoice = Invoice::scope()->invoices()->orderBy('id')->first()) {
            $invoice->load('account', 'client.contacts', 'invoice_items');
            $invoice->invoice_date = Utils::fromSqlDate($invoice->invoice_date);
            $invoice->due_date = Utils::fromSqlDate($invoice->due_date);
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
            $client->vat_number = $account->vat_number ? '1234567890' : '';
            $client->id_number = $account->id_number ? '1234567890' : '';

            if ($account->customLabel('client1')) {
                $client->custom_value1 = '0000';
            }

            if ($account->customLabel('client2')) {
                $client->custom_value2 = '0000';
            }

            $invoice = new stdClass();
            $invoice->invoice_number = '0000';
            $invoice->invoice_date = Utils::fromSqlDate(date('Y-m-d'));
            $invoice->account = json_decode($account->toJson());
            $invoice->amount = 100;
            $invoice->balance = 100;

            if ($account->customLabel('invoice_text1')) {
                $invoice->custom_text_value1 = '0000';
            }

            if ($account->customLabel('invoice_text2')) {
                $invoice->custom_text_value2 = '0000';
            }

            $invoice->terms = trim($account->invoice_terms);
            $invoice->invoice_footer = trim($account->invoice_footer);

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

            if ($account->customLabel('product1')) {
                $invoiceItem->custom_value1 = '0000';
            }

            if ($account->customLabel('product2')) {
                $invoiceItem->custom_value2 = '0000';
            }

            $document->base64 = 'data:image/jpeg;base64,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';

            $invoice->client = $client;
            $invoice->invoice_items = [$invoiceItem];
            //$invoice->documents = $account->hasFeature(FEATURE_DOCUMENTS) ? [$document] : [];
            $invoice->documents = [];
        }

        $data['account'] = $account;
        $data['invoice'] = $invoice;
        $data['invoiceLabels'] = json_decode($account->invoice_labels) ?: [];
        $data['title'] = trans('texts.invoice_design');
        $data['invoiceDesigns'] = InvoiceDesign::getDesigns();
        $data['invoiceFonts'] = \Illuminate\Support\Facades\Cache::get('fonts');
        $data['section'] = $section;
        $data['pageSizes'] = array_combine(InvoiceDesign::$pageSizes, InvoiceDesign::$pageSizes);

        $design = false;
        foreach ($data['invoiceDesigns'] as $item) {
            if ($item->id == $account->invoice_design_id) {
                $design = $item->javascript;
                break;
            }
        }

        if ($section == ACCOUNT_CUSTOMIZE_DESIGN) {
            $data['customDesign'] = ($custom = $account->getCustomDesign(request()->design_id)) ? $custom : $design;
        }

        return \Illuminate\Support\Facades\View::make('accounts.' . $section, $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showClientPortal()
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account->load('country');
        $css = $account->client_view_css ?: '';

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
            if ($account->getGatewayByType($type)) {
                $alias = GatewayType::getAliasFromId($type);
                $options[$alias] = trans('texts.' . $alias);
            }
        }

        $data = [
            'client_view_css'        => $css,
            'enable_portal_password' => $account->enable_portal_password,
            'send_portal_password'   => $account->send_portal_password,
            'title'                  => trans('texts.client_portal'),
            'section'                => ACCOUNT_CLIENT_PORTAL,
            'account'                => $account,
            'products'               => Product::scope()->orderBy('product_key')->get(),
            'gateway_types'          => $options,
        ];

        return \Illuminate\Support\Facades\View::make('accounts.client_portal', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    private function showTemplates()
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account->load('country');
        $data['account'] = $account;
        $data['templates'] = [];
        $data['defaultTemplates'] = [];
        foreach (AccountEmailSettings::$templates as $type) {
            $data['templates'][$type] = [
                'subject'  => $account->getEmailSubject($type),
                'template' => $account->getEmailTemplate($type),
            ];
            $data['defaultTemplates'][$type] = [
                'subject'  => $account->getDefaultEmailSubject($type),
                'template' => $account->getDefaultEmailTemplate($type),
            ];
        }

        $data['title'] = trans('texts.email_templates');

        return \Illuminate\Support\Facades\View::make('accounts.templates_and_reminders', $data);
    }

    /**
     * @return RedirectResponse
     */
    private function saveAccountManagement()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $account = $user->account;
        $modules = \Illuminate\Support\Facades\Request::input('modules');

        if (Utils::isSelfHost()) {
            // get all custom modules, including disabled
            $custom_modules = collect(\Illuminate\Support\Facades\Request::input('custom_modules'))->each(function ($item, $key): void {
                $module = Module::find($item);
                if ($module && $module->disabled()) {
                    $module->enable();
                }
            });

            (Module::toCollection()->diff($custom_modules))->each(function ($item, $key): void {
                if ($item->enabled()) {
                    $item->disable();
                }
            });
        }

        $user->force_pdfjs = (bool) \Illuminate\Support\Facades\Request::input('force_pdfjs');
        $user->save();

        $account->live_preview = (bool) \Illuminate\Support\Facades\Request::input('live_preview');

        // Automatically disable live preview when using a large font
        $fonts = \Illuminate\Support\Facades\Cache::get('fonts')->filter(function ($font) use ($account): bool {
            if ($font->google_font) {
                return false;
            }

            return $font->id == $account->header_font_id || $font->id == $account->body_font_id;
        });
        if ($account->live_preview && $fonts->count()) {
            $account->live_preview = false;
            \Illuminate\Support\Facades\Session::flash('warning', trans('texts.live_preview_disabled'));
        }

        $account->enabled_modules = $modules ? array_sum($modules) : 0;
        $account->save();

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_MANAGEMENT);
    }

    /**
     * @return RedirectResponse
     */
    private function saveCustomizeDesign()
    {
        $designId = (int) (\Illuminate\Support\Facades\Request::input('design_id')) ?: CUSTOM_DESIGN1;
        $field = 'custom_design' . ($designId - 10);

        if (\Illuminate\Support\Facades\Auth::user()->account->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN)) {
            $account = \Illuminate\Support\Facades\Auth::user()->account;
            if ( ! $account->custom_design1) {
                $account->invoice_design_id = CUSTOM_DESIGN1;
            }

            $account->{$field} = \Illuminate\Support\Facades\Request::input('custom_design');
            $account->save();

            \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));
        }

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_CUSTOMIZE_DESIGN . '?design_id=' . $designId);
    }

    /**
     * @return RedirectResponse
     */
    private function saveEmailTemplates()
    {
        if (\Illuminate\Support\Facades\Auth::user()->account->hasFeature(FEATURE_EMAIL_TEMPLATES_REMINDERS)) {
            $account = \Illuminate\Support\Facades\Auth::user()->account;

            foreach (AccountEmailSettings::$templates as $type) {
                $subjectField = 'email_subject_' . $type;
                $subject = \Illuminate\Support\Facades\Request::input($subjectField, $account->getEmailSubject($type));
                $account->account_email_settings->{$subjectField} = ($subject == $account->getDefaultEmailSubject($type) ? null : $subject);

                $bodyField = 'email_template_' . $type;
                $body = \Illuminate\Support\Facades\Request::input($bodyField, $account->getEmailTemplate($type));
                $account->account_email_settings->{$bodyField} = ($body == $account->getDefaultEmailTemplate($type) ? null : $body);
            }

            foreach ([TEMPLATE_REMINDER1, TEMPLATE_REMINDER2, TEMPLATE_REMINDER3] as $type) {
                $enableField = 'enable_' . $type;
                $account->{$enableField} = (bool) \Illuminate\Support\Facades\Request::input($enableField);
                $account->{'num_days_' . $type} = \Illuminate\Support\Facades\Request::input('num_days_' . $type);
                $account->{'field_' . $type} = \Illuminate\Support\Facades\Request::input('field_' . $type);
                $account->{'direction_' . $type} = \Illuminate\Support\Facades\Request::input('field_' . $type) == REMINDER_FIELD_INVOICE_DATE ? REMINDER_DIRECTION_AFTER : \Illuminate\Support\Facades\Request::input('direction_' . $type);

                $number = preg_replace('/[^0-9]/', '', $type);
                $account->account_email_settings->{sprintf('late_fee%s_amount', $number)} = \Illuminate\Support\Facades\Request::input(sprintf('late_fee%s_amount', $number));
                $account->account_email_settings->{sprintf('late_fee%s_percent', $number)} = \Illuminate\Support\Facades\Request::input(sprintf('late_fee%s_percent', $number));
            }

            $account->enable_reminder4 = (bool) \Illuminate\Support\Facades\Request::input('enable_reminder4');
            $account->account_email_settings->frequency_id_reminder4 = \Illuminate\Support\Facades\Request::input('frequency_id_reminder4');

            $account->save();
            $account->account_email_settings->save();

            \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));
        }

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_TEMPLATES_AND_REMINDERS);
    }

    /**
     * @return RedirectResponse
     */
    private function saveTaxRates()
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $account->fill(\Illuminate\Support\Facades\Request::all());
        $account->save();

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_TAX_RATES);
    }

    /**
     * @return RedirectResponse
     */
    private function saveProducts()
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;

        $account->show_product_notes = (bool) \Illuminate\Support\Facades\Request::input('show_product_notes');
        $account->fill_products = (bool) \Illuminate\Support\Facades\Request::input('fill_products');
        $account->update_products = (bool) \Illuminate\Support\Facades\Request::input('update_products');
        $account->convert_products = (bool) \Illuminate\Support\Facades\Request::input('convert_products');
        $account->save();

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_PRODUCTS);
    }

    /**
     * @return $this|RedirectResponse
     */
    private function saveInvoiceSettings()
    {
        if (\Illuminate\Support\Facades\Auth::user()->account->hasFeature(FEATURE_INVOICE_SETTINGS)) {
            $rules = [];
            foreach ([ENTITY_INVOICE, ENTITY_QUOTE, ENTITY_CLIENT] as $entityType) {
                if (\Illuminate\Support\Facades\Request::input($entityType . '_number_type') == 'pattern') {
                    $rules[$entityType . '_number_pattern'] = 'has_counter';
                }
            }

            if (\Illuminate\Support\Facades\Request::input('credit_number_enabled')) {
                $rules['credit_number_prefix'] = 'required_without:credit_number_pattern';
                $rules['credit_number_pattern'] = 'required_without:credit_number_prefix';
            }

            $validator = \Illuminate\Support\Facades\Validator::make(\Illuminate\Support\Facades\Request::all(), $rules);

            if ($validator->fails()) {
                return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_INVOICE_SETTINGS)
                    ->withErrors($validator)
                    ->withInput();
            }

            $account = \Illuminate\Support\Facades\Auth::user()->account;
            $account->custom_value1 = \Illuminate\Support\Facades\Request::input('custom_value1');
            $account->custom_value2 = \Illuminate\Support\Facades\Request::input('custom_value2');
            $account->custom_invoice_taxes1 = (bool) \Illuminate\Support\Facades\Request::input('custom_invoice_taxes1');
            $account->custom_invoice_taxes2 = (bool) \Illuminate\Support\Facades\Request::input('custom_invoice_taxes2');
            $account->custom_fields = request()->custom_fields;
            $account->invoice_number_padding = \Illuminate\Support\Facades\Request::input('invoice_number_padding');
            $account->invoice_number_counter = \Illuminate\Support\Facades\Request::input('invoice_number_counter');
            $account->quote_number_prefix = \Illuminate\Support\Facades\Request::input('quote_number_prefix');
            $account->share_counter = (bool) \Illuminate\Support\Facades\Request::input('share_counter');
            $account->invoice_terms = \Illuminate\Support\Facades\Request::input('invoice_terms');
            $account->invoice_footer = \Illuminate\Support\Facades\Request::input('invoice_footer');
            $account->quote_terms = \Illuminate\Support\Facades\Request::input('quote_terms');
            $account->auto_convert_quote = \Illuminate\Support\Facades\Request::input('auto_convert_quote');
            $account->auto_archive_quote = \Illuminate\Support\Facades\Request::input('auto_archive_quote');
            $account->auto_archive_invoice = \Illuminate\Support\Facades\Request::input('auto_archive_invoice');
            $account->auto_email_invoice = \Illuminate\Support\Facades\Request::input('auto_email_invoice');
            $account->recurring_invoice_number_prefix = \Illuminate\Support\Facades\Request::input('recurring_invoice_number_prefix');

            $account->client_number_prefix = trim(\Illuminate\Support\Facades\Request::input('client_number_prefix'));
            $account->client_number_pattern = trim(\Illuminate\Support\Facades\Request::input('client_number_pattern'));
            $account->client_number_counter = \Illuminate\Support\Facades\Request::input('client_number_counter');
            $account->credit_number_counter = \Illuminate\Support\Facades\Request::input('credit_number_counter');
            $account->credit_number_prefix = trim(\Illuminate\Support\Facades\Request::input('credit_number_prefix'));
            $account->credit_number_pattern = trim(\Illuminate\Support\Facades\Request::input('credit_number_pattern'));
            $account->reset_counter_frequency_id = \Illuminate\Support\Facades\Request::input('reset_counter_frequency_id');
            $account->reset_counter_date = $account->reset_counter_frequency_id ? Utils::toSqlDate(\Illuminate\Support\Facades\Request::input('reset_counter_date')) : null;

            if (\Illuminate\Support\Facades\Request::has('recurring_hour')) {
                $account->recurring_hour = \Illuminate\Support\Facades\Request::input('recurring_hour');
            }

            if ( ! $account->share_counter) {
                $account->quote_number_counter = \Illuminate\Support\Facades\Request::input('quote_number_counter');
            }

            foreach ([ENTITY_INVOICE, ENTITY_QUOTE, ENTITY_CLIENT] as $entityType) {
                if (\Illuminate\Support\Facades\Request::input($entityType . '_number_type') == 'prefix') {
                    $account->{$entityType . '_number_prefix'} = trim(\Illuminate\Support\Facades\Request::input($entityType . '_number_prefix'));
                    $account->{$entityType . '_number_pattern'} = null;
                } else {
                    $account->{$entityType . '_number_pattern'} = trim(\Illuminate\Support\Facades\Request::input($entityType . '_number_pattern'));
                    $account->{$entityType . '_number_prefix'} = null;
                }
            }

            if ( ! $account->share_counter
                && $account->invoice_number_prefix == $account->quote_number_prefix
                && $account->invoice_number_pattern == $account->quote_number_pattern) {
                \Illuminate\Support\Facades\Session::flash('error', trans('texts.invalid_counter'));

                return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_INVOICE_SETTINGS)->withInput();
            }

            $account->save();
            \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));
        }

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_INVOICE_SETTINGS);
    }

    /**
     * @return RedirectResponse
     */
    private function saveInvoiceDesign()
    {
        if (\Illuminate\Support\Facades\Auth::user()->account->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN)) {
            $account = \Illuminate\Support\Facades\Auth::user()->account;
            $account->hide_quantity = (bool) \Illuminate\Support\Facades\Request::input('hide_quantity');
            $account->hide_paid_to_date = (bool) \Illuminate\Support\Facades\Request::input('hide_paid_to_date');
            $account->all_pages_header = (bool) \Illuminate\Support\Facades\Request::input('all_pages_header');
            $account->all_pages_footer = (bool) \Illuminate\Support\Facades\Request::input('all_pages_footer');
            $account->invoice_embed_documents = (bool) \Illuminate\Support\Facades\Request::input('invoice_embed_documents');
            $account->header_font_id = \Illuminate\Support\Facades\Request::input('header_font_id');
            $account->body_font_id = \Illuminate\Support\Facades\Request::input('body_font_id');
            $account->primary_color = \Illuminate\Support\Facades\Request::input('primary_color');
            $account->secondary_color = \Illuminate\Support\Facades\Request::input('secondary_color');
            $account->invoice_design_id = \Illuminate\Support\Facades\Request::input('invoice_design_id');
            $account->quote_design_id = \Illuminate\Support\Facades\Request::input('quote_design_id');
            $account->font_size = (int) (\Illuminate\Support\Facades\Request::input('font_size'));
            $account->page_size = \Illuminate\Support\Facades\Request::input('page_size');
            $account->background_image_id = Document::getPrivateId(request()->background_image_id);

            $labels = [];
            foreach (Account::$customLabels as $field) {
                $labels[$field] = \Illuminate\Support\Facades\Request::input('labels_' . $field);
            }

            $account->invoice_labels = json_encode($labels);
            $account->invoice_fields = \Illuminate\Support\Facades\Request::input('invoice_fields_json');

            $account->save();

            \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));
        }

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_INVOICE_DESIGN);
    }

    /**
     * @return RedirectResponse
     */
    private function saveNotifications()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $user->notify_sent = \Illuminate\Support\Facades\Request::input('notify_sent');
        $user->notify_viewed = \Illuminate\Support\Facades\Request::input('notify_viewed');
        $user->notify_paid = \Illuminate\Support\Facades\Request::input('notify_paid');
        $user->notify_approved = \Illuminate\Support\Facades\Request::input('notify_approved');
        $user->only_notify_owned = \Illuminate\Support\Facades\Request::input('only_notify_owned');
        $user->slack_webhook_url = \Illuminate\Support\Facades\Request::input('slack_webhook_url');
        $user->save();

        $account = $user->account;
        $account->fill(request()->all());
        $account->save();

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_NOTIFICATIONS);
    }

    /**
     * @return RedirectResponse
     */
    private function saveLocalization()
    {
        /** @var \App\Models\Account $account */
        $account = \Illuminate\Support\Facades\Auth::user()->account;

        $account->timezone_id = \Illuminate\Support\Facades\Request::input('timezone_id') ?: null;
        $account->date_format_id = \Illuminate\Support\Facades\Request::input('date_format_id') ?: null;
        $account->datetime_format_id = \Illuminate\Support\Facades\Request::input('datetime_format_id') ?: null;
        $account->currency_id = \Illuminate\Support\Facades\Request::input('currency_id') ?: 1; // US Dollar
        $account->language_id = \Illuminate\Support\Facades\Request::input('language_id') ?: 1; // English
        $account->military_time = (bool) \Illuminate\Support\Facades\Request::input('military_time');
        $account->show_currency_code = (bool) \Illuminate\Support\Facades\Request::input('show_currency_code');
        $account->start_of_week = \Illuminate\Support\Facades\Request::input('start_of_week') ?: 0;
        $account->financial_year_start = \Illuminate\Support\Facades\Request::input('financial_year_start') ?: null;
        $account->save();

        event(new UserSettingsChanged());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_LOCALIZATION);
    }

    /**
     * @return RedirectResponse
     */
    private function saveOnlinePayments()
    {
        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $account->token_billing_type_id = \Illuminate\Support\Facades\Request::input('token_billing_type_id');
        $account->auto_bill_on_due_date = (bool) (\Illuminate\Support\Facades\Request::input('auto_bill_on_due_date'));
        $account->gateway_fee_enabled = (bool) (\Illuminate\Support\Facades\Request::input('gateway_fee_enabled'));
        $account->send_item_details = (bool) (\Illuminate\Support\Facades\Request::input('send_item_details'));

        $account->save();

        event(new UserSettingsChanged());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_PAYMENTS);
    }
}
