<?php

namespace App\Http\Controllers\ClientAuth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Utils;

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
    protected $redirectTo = '/client/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:client', ['except' => 'getLogoutWrapper']);
    }

    public function showLoginForm()
    {
        $subdomain = Utils::getSubdomain(\Illuminate\Support\Facades\Request::server('HTTP_HOST'));
        $hasAccountIndentifier = request()->account_key || ($subdomain && ! in_array($subdomain, ['www', 'app']));

        if ( ! session('contact_key')) {
            if (Utils::isNinja()) {
                if ( ! $hasAccountIndentifier) {
                    return redirect('/client/session_expired');
                }
            } elseif ( ! $hasAccountIndentifier && Account::count() > 1) {
                return redirect('/client/session_expired');
            }
        }

        return view('clientauth.login')->with(['clientauth' => true]);
    }

    public function getSessionExpired()
    {
        return view('clientauth.sessionexpired')->with(['clientauth' => true]);
    }

    /**
     * @return Response
     */
    public function getLogoutWrapper(Request $request)
    {
        self::logout($request);

        return redirect('/client/login?account_key=' . $request->account_key);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard('client');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        if ($contactKey = session('contact_key')) {
            $credentials = $request->only('password');
            $credentials['contact_key'] = $contactKey;
        } else {
            $credentials = $request->only('email', 'password');
            $account = false;

            // resolve the email to a contact/account
            if ( ! Utils::isNinja() && Account::count() == 1) {
                $account = Account::first();
            } elseif ($accountKey = request()->account_key) {
                $account = Account::whereAccountKey($accountKey)->first();
            } else {
                $subdomain = Utils::getSubdomain(\Illuminate\Support\Facades\Request::server('HTTP_HOST'));
                if ($subdomain && $subdomain != 'app') {
                    $account = Account::whereSubdomain($subdomain)->first();
                }
            }

            if ($account) {
                $credentials['account_id'] = $account->id;
            } else {
                abort(500, 'Account not resolved in client login');
            }
        }

        return $credentials;
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request $request
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
     * Validate the user login request - don't require the email.
     *
     * @param Request $request
     *
     * @return void
     */
    protected function validateLogin(Request $request): void
    {
        $rules = [
            'password' => 'required',
        ];

        if ( ! session('contact_key')) {
            $rules['email'] = 'required|email';
        }

        $this->validate($request, $rules);
    }

    /**
     * Send the post-authentication response.
     *
     * @param Request         $request
     * @param Authenticatable $user
     *
     * @return Response
     */
    private function authenticated(Request $request, Authenticatable $contact)
    {
        session(['contact_key' => $contact->contact_key]);

        return redirect()->intended($this->redirectPath());
    }
}
