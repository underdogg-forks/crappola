<?php

namespace App\Http\Controllers;

use App\Events\UserSignedUp;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\LookupUser;
use App\Models\User;
use App\Ninja\OAuth\OAuth;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Transformers\AccountTransformer;
use App\Ninja\Transformers\UserAccountTransformer;
use Auth;
use Carbon;
use Crypt;
use Google2FA;
use Illuminate\Http\Request;
use Response;
use App\Libraries\Utils;

class AccountApiController extends BaseAPIController
{
    protected $companyRepo;

    public function __construct(AccountRepository $companyRepo)
    {
        parent::__construct();

        $this->accountRepo = $companyRepo;
    }

    public function ping(Request $request)
    {
        $headers = Utils::getApiHeaders();

        // Legacy support for Zapier
        if (request()->v2) {
            return $this->response(auth()->user()->email);
        }

        return Response::make(RESULT_SUCCESS, 200, $headers);
    }

    public function register(RegisterRequest $request)
    {
        if (!LookupUser::validateField('email', $request->email)) {
            return $this->errorResponse(['message' => trans('texts.email_taken')], 500);
        }

        $company = $this->accountRepo->create($request->first_name, $request->last_name, $request->email, $request->password);
        $user = $company->users()->first();

        Auth::login($user);
        event(new UserSignedUp());

        return $this->processLogin($request);
    }

    public function login(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();

        if ($user && $user->failed_logins >= MAX_FAILED_LOGINS) {
            sleep(ERROR_DELAY);

            return $this->errorResponse(['message' => 'Invalid credentials'], 401);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // TODO remove token_name check once legacy apps are deactivated
            if ($user->google_2fa_secret && strpos($request->token_name, 'invoice-ninja-') !== false) {
                $secret = Crypt::decrypt($user->google_2fa_secret);
                if (!$request->one_time_password) {
                    return $this->errorResponse(['message' => 'OTP_REQUIRED'], 401);
                } elseif (!Google2FA::verifyKey($secret, $request->one_time_password)) {
                    return $this->errorResponse(['message' => 'Invalid one time password'], 401);
                }
            }
            if ($user && $user->failed_logins > 0) {
                $user->failed_logins = 0;
                $user->save();
            }

            return $this->processLogin($request);
        }
        error_log('login failed');
        if ($user) {
            $user->failed_logins = $user->failed_logins + 1;
            $user->save();
        }
        sleep(ERROR_DELAY);

        return $this->errorResponse(['message' => 'Invalid credentials'], 401);
    }

    private function processLogin(Request $request, $createToken = true)
    {
        // Create a new token only if one does not already exist
        $user = Auth::user();
        $company = $user->company;

        if ($createToken) {
            $this->accountRepo->createTokens($user, $request->token_name);
        }

        $users = $this->accountRepo->findUsers($user, 'company.account_tokens');
        $transformer = new UserAccountTransformer($company, $request->serializer, $request->token_name);
        $data = $this->createCollection($users, $transformer, 'user_account');

        if (request()->include_static) {
            $data = [
                'companies' => $data,
                'static' => Utils::getStaticData($company->getLocale()),
                'version' => NINJA_VERSION,
            ];
        }

        return $this->response($data);
    }

    public function getStaticData()
    {
        return $this->response(Utils::getStaticData());
    }

    public function refresh(Request $request)
    {
        return $this->processLogin($request, false);
    }

    public function show(Request $request)
    {
        $company = Auth::user()->company;
        $updatedAt = $request->updated_at ? date('Y-m-d H:i:s', $request->updated_at) : false;

        $transformer = new AccountTransformer(null, $request->serializer);
        $company->load(array_merge($transformer->getDefaultIncludes(), ['projects.client']));
        $company = $this->createItem($company, $transformer, 'company');

        return $this->response($company);
    }

    public function getUserAccounts(Request $request)
    {
        $user = Auth::user();

        $users = $this->accountRepo->findUsers($user, 'company.account_tokens');
        $transformer = new UserAccountTransformer($user->company, $request->serializer, $request->token_name);
        $data = $this->createCollection($users, $transformer, 'user_account');

        return $this->response($data);
    }

    public function update(UpdateAccountRequest $request)
    {
        $company = Auth::user()->company;
        $this->accountRepo->save($request->input(), $company);

        $transformer = new AccountTransformer(null, $request->serializer);
        $company = $this->createItem($company, $transformer, 'company');

        return $this->response($company);
    }

    public function addDeviceToken(Request $request)
    {
        $company = Auth::user()->company;

        //scan if this user has a token already registered (tokens can change, so we need to use the users email as key)
        $devices = json_decode($company->devices, true);

        for ($x = 0; $x < count($devices); $x++) {
            if ($devices[$x]['email'] == $request->email) {
                $devices[$x]['token'] = $request->token; //update
                $devices[$x]['device'] = $request->device;
                $company->devices = json_encode($devices);
                $company->save();
                $devices[$x]['account_key'] = $company->account_key;

                return $this->response($devices[$x]);
            }
        }

        //User does not have a device, create new record

        $newDevice = [
            'token' => $request->token,
            'email' => $request->email,
            'device' => $request->device,
            'account_key' => $company->account_key,
            'notify_sent' => true,
            'notify_viewed' => true,
            'notify_approved' => true,
            'notify_paid' => true,
        ];

        $devices[] = $newDevice;
        $company->devices = json_encode($devices);
        $company->save();

        return $this->response($newDevice);
    }

    public function removeDeviceToken(Request $request)
    {
        $company = Auth::user()->company;

        $devices = json_decode($company->devices, true);

        for ($x = 0; $x < count($devices); $x++) {
            if ($request->token == $devices[$x]['token']) {
                unset($devices[$x]);
            }
        }

        $company->devices = json_encode(array_values($devices));
        $company->save();

        return $this->response(['success']);
    }

    public function updatePushNotifications(Request $request)
    {
        $company = Auth::user()->company;

        $devices = json_decode($company->devices, true);

        if (count($devices) < 1) {
            return $this->errorResponse(['message' => 'No registered devices.'], 400);
        }

        for ($x = 0; $x < count($devices); $x++) {
            if ($devices[$x]['email'] == Auth::user()->username) {
                $newDevice = [
                    'token' => $devices[$x]['token'],
                    'email' => $devices[$x]['email'],
                    'device' => $devices[$x]['device'],
                    'account_key' => $company->account_key,
                    'notify_sent' => $request->notify_sent,
                    'notify_viewed' => $request->notify_viewed,
                    'notify_approved' => $request->notify_approved,
                    'notify_paid' => $request->notify_paid,
                ];

                $devices[$x] = $newDevice;
                $company->devices = json_encode($devices);
                $company->save();

                return $this->response($newDevice);
            }
        }
    }

    public function oauthLogin(Request $request)
    {
        $user = false;
        $token = $request->input('token');
        $provider = $request->input('provider');

        $oAuth = new OAuth();
        $user = $oAuth->getProvider($provider)->getTokenResponse($token);

        /*
        if ($user->google_2fa_secret && strpos($request->token_name, 'invoice-ninja-') !== false) {
            $secret = \Crypt::decrypt($user->google_2fa_secret);
            if (! $request->one_time_password) {
                return $this->errorResponse(['message' => 'OTP_REQUIRED'], 401);
            } elseif (! \Google2FA::verifyKey($secret, $request->one_time_password)) {
                return $this->errorResponse(['message' => 'Invalid one time password'], 401);
            }
        }
        */

        if ($user) {
            Auth::login($user);

            return $this->processLogin($request);
        }

        return $this->errorResponse(['message' => 'Invalid credentials'], 401);
    }

    public function iosSubscriptionStatus(): void
    {
        //stubbed for iOS callbacks
    }

    public function upgrade(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        $companyPlan = $company->companyPlan;
        $orderId = $request->order_id;
        $timestamp = $request->timestamp;
        $productId = $request->product_id;

        if (Carbon::createFromTimestamp($timestamp) < Carbon::now()->subYear()) {
            return '{"message":"The order is expired"}';
        }

        if ($productId == 'v1_pro_yearly') {
            $companyPlan->plan = PLAN_PRO;
            $companyPlan->num_users = 1;
            $companyPlan->plan_price = PLAN_PRICE_PRO_MONTHLY * 10;
        } elseif ($productId == 'v1_enterprise_2_yearly') {
            $companyPlan->plan = PLAN_ENTERPRISE;
            $companyPlan->num_users = 2;
            $companyPlan->plan_price = PLAN_PRICE_ENTERPRISE_MONTHLY_2 * 10;
        } elseif ($productId == 'v1_enterprise_5_yearly') {
            $companyPlan->plan = PLAN_ENTERPRISE;
            $companyPlan->num_users = 5;
            $companyPlan->plan_price = PLAN_PRICE_ENTERPRISE_MONTHLY_5 * 10;
        } elseif ($productId == 'v1_enterprise_10_yearly') {
            $companyPlan->plan = PLAN_ENTERPRISE;
            $companyPlan->num_users = 10;
            $companyPlan->plan_price = PLAN_PRICE_ENTERPRISE_MONTHLY_10 * 10;
        } elseif ($productId == 'v1_enterprise_20_yearly') {
            $companyPlan->plan = PLAN_ENTERPRISE;
            $companyPlan->num_users = 20;
            $companyPlan->plan_price = PLAN_PRICE_ENTERPRISE_MONTHLY_20 * 10;
        }

        $companyPlan->app_store_order_id = $orderId;
        $companyPlan->plan_term = PLAN_TERM_YEARLY;
        $companyPlan->plan_started = $companyPlan->plan_started ?: date('Y-m-d');
        $companyPlan->plan_paid = date('Y-m-d');
        $companyPlan->plan_expires = Carbon::createFromTimestamp($timestamp)->addYear()->format('Y-m-d');
        $companyPlan->trial_plan = null;
        $companyPlan->save();

        return '{"message":"success"}';
    }
}
