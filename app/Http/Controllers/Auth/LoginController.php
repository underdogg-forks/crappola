<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Events\UserLoggedIn;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateTwoFactorRequest;
use App\Libraries\Utils;
use App\Models\User;
use App\Ninja\Repositories\AccountRepository;
use Cache;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogoutWrapper']);
    }

    /**
     * @return Response
     */
    public function getLoginWrapper(Request $request)
    {
        if (auth()->check()) {
            return redirect('/');
        }

        if ( ! Utils::isNinja() && ! User::count()) {
            return redirect()->to('/setup');
        }

        if (Utils::isNinja() && ! Utils::isTravis()) {
            // make sure the user is on SITE_URL/login to ensure OAuth works
            $requestURL = request()->url();
            $loginURL = SITE_URL . '/login';
            $subdomain = Utils::getSubdomain(request()->url());
            if ($requestURL != $loginURL && ! mb_strstr($subdomain, 'webapp-')) {
                return redirect()->to($loginURL);
            }
        }

        return self::showLoginForm();
    }

    /**
     * @return Response
     */
    public function postLoginWrapper(Request $request)
    {
        if (auth()->check()) {
            auth()->user()->id;
        }

        $user = User::where('email', '=', $request->input('email'))->first();

        if ($user && $user->failed_logins >= MAX_FAILED_LOGINS) {
            session()->flash('error', trans('texts.invalid_credentials'));

            return redirect()->to('login');
        }

        $response = self::login($request);

        if (auth()->check()) {
            /*
            $users = false;
            // we're linking a new account
            if ($request->link_accounts && $userId && Auth::user()->id != $userId) {
                $users = $this->accountRepo->associateAccounts($userId, Auth::user()->id);
                Session::flash('message', trans('texts.associated_accounts'));
                // check if other accounts are linked
            } else {
                $users = $this->accountRepo->loadAccounts(Auth::user()->id);
            }
            */
        } else {
            $stacktrace = sprintf("%s %s %s %s\n", Carbon::now()->format('Y-m-d h:i:s'), $request->input('email'), \Illuminate\Support\Facades\Request::getClientIp(), Arr::get($_SERVER, 'HTTP_USER_AGENT'));
            if (config('app.log') == 'single') {
                file_put_contents(storage_path('logs/failed-logins.log'), $stacktrace, FILE_APPEND);
            } else {
                Utils::logError('[failed login] ' . $stacktrace);
            }

            if ($user) {
                $user->failed_logins += 1;
                $user->save();
            }
        }

        return $response;
    }

    /**
     * @return Response
     */
    public function getValidateToken()
    {
        if (session('2fa:user:id')) {
            return view('auth.two_factor');
        }

        return redirect('login');
    }

    /**
     * @param App\Http\Requests\ValidateSecretRequest $request
     *
     * @return Response
     */
    public function postValidateToken(ValidateTwoFactorRequest $request)
    {
        //get user id and create cache key
        $userId = session()->pull('2fa:user:id');
        $key = $userId . ':' . $request->totp;

        //use cache to store token to blacklist
        \Illuminate\Support\Facades\Cache::add($key, true, 4 * 60);

        //login and redirect user
        auth()->loginUsingId($userId);
        Event::dispatch(new UserLoggedIn());

        if ($trust = request()->trust) {
            $user = auth()->user();
            if ( ! $user->remember_2fa_token) {
                $user->remember_2fa_token = Str::random(60);
                $user->save();
            }

            $minutes = $trust == 30 ? 60 * 27 * 30 : 2628000;
            cookie()->queue('remember_2fa_' . sha1($user->id), $user->remember_2fa_token, $minutes);
        }

        return redirect()->intended($this->redirectTo);
    }

    /**
     * @return Response
     */
    public function getLogoutWrapper(Request $request)
    {
        if (auth()->check() && ! auth()->user()->email && ! auth()->user()->registered) {
            if (request()->force_logout) {
                $account = auth()->user()->account;
                app(AccountRepository::class)->unlinkAccount($account);

                if ( ! $account->hasMultipleAccounts()) {
                    $account->company->forceDelete();
                }

                $account->forceDelete();
            } else {
                return redirect('/');
            }
        }

        $response = self::logout($request);

        $reason = htmlentities(request()->reason);
        if ($reason !== '' && $reason !== '0' && Lang::has(sprintf('texts.%s_logout', $reason))) {
            session()->flash('warning', trans(sprintf('texts.%s_logout', $reason)));
        }

        return $response;
    }

    /**
     * Get the failed login response instance.
     *
     *
     * @return RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => trans('texts.invalid_credentials'),
            ]);
    }

    /**
     * Send the post-authentication response.
     *
     *
     * @return Response
     */
    private function authenticated(Authenticatable $user)
    {
        if ($user->google_2fa_secret) {
            $cookie = false;
            if ($user->remember_2fa_token) {
                $cookie = Cookie::get('remember_2fa_' . sha1($user->id));
            }

            if ($cookie && hash_equals($user->remember_2fa_token, $cookie)) {
                // do nothing
            } else {
                auth()->logout();
                session()->put('2fa:user:id', $user->id);

                return redirect('/validate_two_factor/' . $user->account->account_key);
            }
        }

        Event::dispatch(new UserLoggedIn());

        return redirect()->intended($this->redirectTo);
    }
}
