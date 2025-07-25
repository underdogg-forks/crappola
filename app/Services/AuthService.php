<?php

namespace App\Services;

use App\Events\UserLoggedIn;
use App\Libraries\Utils;
use App\Models\LookupUser;
use App\Ninja\Repositories\AccountRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Socialite;

/**
 * Class AuthService.
 */
class AuthService
{
    public static $providers = [
        1 => SOCIAL_GOOGLE,
        2 => SOCIAL_FACEBOOK,
        3 => SOCIAL_GITHUB,
        4 => SOCIAL_LINKEDIN,
    ];

    /**
     * @var AccountRepository
     */
    private $accountRepo;

    /**
     * AuthService constructor.
     *
     * @param AccountRepository $repo
     */
    public function __construct(AccountRepository $repo)
    {
        $this->accountRepo = $repo;
    }

    public static function getProviders() {}

    /**
     * @param $provider
     *
     * @return mixed
     */
    public static function getProviderId($provider)
    {
        return array_search(mb_strtolower($provider), array_map('strtolower', self::$providers));
    }

    /**
     * @param $providerId
     *
     * @return mixed|string
     */
    public static function getProviderName($providerId)
    {
        return $providerId ? self::$providers[$providerId] : '';
    }

    /**
     * @param $provider
     * @param $hasCode
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function execute($provider, $hasCode)
    {
        if ( ! $hasCode) {
            return $this->getAuthorization($provider);
        }

        $socialiteUser = Socialite::driver($provider)->user();
        $providerId = self::getProviderId($provider);

        $email = $socialiteUser->email;
        $oauthUserId = $socialiteUser->id;
        $name = Utils::splitName($socialiteUser->name);

        if (Auth::check()) {
            $user = Auth::user();
            $isRegistered = $user->registered;
            $result = $this->accountRepo->updateUserFromOauth($user, $name[0], $name[1], $email, $providerId, $oauthUserId);

            if ($result === true) {
                if ( ! $isRegistered) {
                    Session::flash('warning', trans('texts.success_message'));
                    Session::flash('onReady', 'handleSignedUp();');
                } else {
                    Session::flash('message', trans('texts.updated_settings'));

                    return redirect()->to('/settings/' . ACCOUNT_USER_DETAILS);
                }
            } else {
                Session::flash('error', $result);
            }
        } else {
            LookupUser::setServerByField('oauth_user_key', $providerId . '-' . $oauthUserId);
            if ($user = $this->accountRepo->findUserByOauth($providerId, $oauthUserId)) {
                if ($user->google_2fa_secret) {
                    session(['2fa:user:id' => $user->id]);

                    return redirect('/validate_two_factor/' . $user->account->account_key);
                }
                Auth::login($user);
                event(new UserLoggedIn());
            } else {
                Session::flash('error', trans('texts.invalid_credentials'));

                return redirect()->to('login');
            }
        }

        $redirectTo = Request::input('redirect_to') ? SITE_URL . '/' . ltrim(Request::input('redirect_to'), '/') : 'dashboard';

        return redirect()->to($redirectTo);
    }

    /**
     * @param $provider
     *
     * @return mixed
     */
    private function getAuthorization($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
}
