<?php

namespace App\Services;

use App\Events\UserLoggedIn;
use App\Models\LookupUser;
use App\Ninja\Repositories\AccountRepository;
use Socialite;
use Utils;

/**
 * Class AuthService.
 */
class AuthService
{
    /**
     * @var array
     */
    public static $providers = [
        1 => SOCIAL_GOOGLE,
        2 => SOCIAL_FACEBOOK,
        3 => SOCIAL_GITHUB,
        4 => SOCIAL_LINKEDIN,
    ];

    private readonly \App\Ninja\Repositories\AccountRepository $accountRepo;

    /**
     * AuthService constructor.
     *
     * @param AccountRepository $repo
     */
    public function __construct(AccountRepository $repo)
    {
        $this->accountRepo = $repo;
    }

    public static function getProviders(): void {}

    /**
     * @param $provider
     *
     * @return mixed
     */
    public static function getProviderId($provider): int|string|false
    {
        return array_search(mb_strtolower($provider), array_map('strtolower', self::$providers), true);
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

        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $isRegistered = $user->registered;
            $result = $this->accountRepo->updateUserFromOauth($user, $name[0], $name[1], $email, $providerId, $oauthUserId);

            if ($result === true) {
                if ( ! $isRegistered) {
                    \Illuminate\Support\Facades\Session::flash('warning', trans('texts.success_message'));
                    \Illuminate\Support\Facades\Session::flash('onReady', 'handleSignedUp();');
                } else {
                    \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

                    return redirect()->to('/settings/' . ACCOUNT_USER_DETAILS);
                }
            } else {
                \Illuminate\Support\Facades\Session::flash('error', $result);
            }
        } else {
            LookupUser::setServerByField('oauth_user_key', $providerId . '-' . $oauthUserId);
            if ($user = $this->accountRepo->findUserByOauth($providerId, $oauthUserId)) {
                if ($user->google_2fa_secret) {
                    session(['2fa:user:id' => $user->id]);

                    return redirect('/validate_two_factor/' . $user->account->account_key);
                }

                \Illuminate\Support\Facades\Auth::login($user);
                event(new UserLoggedIn());
            } else {
                \Illuminate\Support\Facades\Session::flash('error', trans('texts.invalid_credentials'));

                return redirect()->to('login');
            }
        }

        $redirectTo = \Illuminate\Support\Facades\Request::input('redirect_to') ? SITE_URL . '/' . ltrim(\Illuminate\Support\Facades\Request::input('redirect_to'), '/') : 'dashboard';

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
