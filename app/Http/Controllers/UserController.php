<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use App\Services\UserService;
use Password;
use Redirect;
use Utils;

class UserController extends BaseController
{
    protected \App\Ninja\Repositories\AccountRepository $accountRepo;

    protected \App\Ninja\Mailers\ContactMailer $contactMailer;

    protected \App\Ninja\Mailers\UserMailer $userMailer;

    protected \App\Services\UserService $userService;

    public function __construct(AccountRepository $accountRepo, ContactMailer $contactMailer, UserMailer $userMailer, UserService $userService)
    {
        //parent::__construct();

        $this->accountRepo = $accountRepo;
        $this->contactMailer = $contactMailer;
        $this->userMailer = $userMailer;
        $this->userService = $userService;
    }

    public function index()
    {
        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
    }

    public function getDatatable()
    {
        return $this->userService->getDatatable(\Illuminate\Support\Facades\Auth::user()->account_id);
    }

    public function forcePDFJS()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $user->force_pdfjs = true;
        $user->save();

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_settings'));

        return \Illuminate\Support\Facades\Redirect::to('/dashboard');
    }

    /**
     * Display the specified resource.
     *
     * @param int   $id
     * @param mixed $publicId
     *
     * @return Response
     */
    public function show($publicId)
    {
        \Illuminate\Support\Facades\Session::reflash();

        return redirect("users/{$publicId}/edit");
    }

    public function edit(string $publicId)
    {
        $user = User::where('account_id', '=', \Illuminate\Support\Facades\Auth::user()->account_id)
            ->where('public_id', '=', $publicId)
            ->withTrashed()
            ->firstOrFail();

        $data = [
            'user'   => $user,
            'method' => 'PUT',
            'url'    => 'users/' . $publicId,
        ];

        return \Illuminate\Support\Facades\View::make('users.edit', $data);
    }

    public function update($publicId)
    {
        return $this->save($publicId);
    }

    public function store()
    {
        return $this->save();
    }

    /**
     * Displays the form for account creation.
     */
    public function create()
    {
        if ( ! \Illuminate\Support\Facades\Auth::user()->registered) {
            \Illuminate\Support\Facades\Session::flash('error', trans('texts.register_to_add_user'));

            return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
        }

        if ( ! \Illuminate\Support\Facades\Auth::user()->confirmed) {
            \Illuminate\Support\Facades\Session::flash('error', trans('texts.confirmation_required', ['link' => link_to('/resend_confirmation', trans('texts.click_here'))]));

            return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
        }

        if (Utils::isNinja() && ! \Illuminate\Support\Facades\Auth::user()->caddAddUsers()) {
            \Illuminate\Support\Facades\Session::flash('error', trans('texts.max_users_reached'));

            return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
        }

        $data = [
            'user'   => null,
            'method' => 'POST',
            'url'    => 'users',
        ];

        return \Illuminate\Support\Facades\View::make('users.edit', $data);
    }

    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('bulk_action');
        $id = \Illuminate\Support\Facades\Request::input('bulk_public_id');

        $user = User::where('account_id', '=', \Illuminate\Support\Facades\Auth::user()->account_id)
            ->where('public_id', '=', $id)
            ->withTrashed()
            ->firstOrFail();

        if ($action === 'archive') {
            $user->delete();
        } else {
            if ( ! \Illuminate\Support\Facades\Auth::user()->caddAddUsers()) {
                return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT)
                    ->with('error', trans('texts.max_users_reached'));
            }

            $user->restore();
        }

        \Illuminate\Support\Facades\Session::flash('message', trans("texts.{$action}d_user"));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
    }

    /**
     * Stores new account.
     *
     * @param mixed $userPublicId
     */
    public function save($userPublicId = false)
    {
        if ( ! \Illuminate\Support\Facades\Auth::user()->hasFeature(FEATURE_USERS)) {
            return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
        }

        $rules = [
            'first_name' => 'required',
            'last_name'  => 'required',
        ];

        if ($userPublicId) {
            $user = User::where('account_id', '=', \Illuminate\Support\Facades\Auth::user()->account_id)
                ->where('public_id', '=', $userPublicId)
                ->withTrashed()
                ->firstOrFail();

            $rules['email'] = 'required|email|unique:users,email,' . $user->id . ',id';
        } else {
            $user = false;
            $rules['email'] = 'required|email|unique:users';
        }

        $validator = \Illuminate\Support\Facades\Validator::make(\Illuminate\Support\Facades\Request::all(), $rules);

        if ($validator->fails()) {
            return \Illuminate\Support\Facades\Redirect::to($userPublicId ? 'users/edit' : 'users/create')
                ->withErrors($validator)
                ->withInput();
        }

        if ( ! \App\Models\LookupUser::validateField('email', \Illuminate\Support\Facades\Request::input('email'), $user)) {
            return \Illuminate\Support\Facades\Redirect::to($userPublicId ? 'users/edit' : 'users/create')
                ->withError(trans('texts.email_taken'))
                ->withInput();
        }

        if ($userPublicId) {
            $user->first_name = trim(\Illuminate\Support\Facades\Request::input('first_name'));
            $user->last_name = trim(\Illuminate\Support\Facades\Request::input('last_name'));
            $user->username = trim(\Illuminate\Support\Facades\Request::input('email'));
            $user->email = trim(\Illuminate\Support\Facades\Request::input('email'));
            if (\Illuminate\Support\Facades\Auth::user()->hasFeature(FEATURE_USER_PERMISSIONS)) {
                $user->is_admin = (bool) (\Illuminate\Support\Facades\Request::input('is_admin'));
                $user->permissions = self::formatUserPermissions(\Illuminate\Support\Facades\Request::input('permissions'));
            }
        } else {
            $lastUser = User::withTrashed()->where('account_id', '=', \Illuminate\Support\Facades\Auth::user()->account_id)
                ->orderBy('public_id', 'DESC')->first();

            $user = new User();
            $user->account_id = \Illuminate\Support\Facades\Auth::user()->account_id;
            $user->first_name = trim(\Illuminate\Support\Facades\Request::input('first_name'));
            $user->last_name = trim(\Illuminate\Support\Facades\Request::input('last_name'));
            $user->username = trim(\Illuminate\Support\Facades\Request::input('email'));
            $user->email = trim(\Illuminate\Support\Facades\Request::input('email'));
            $user->registered = true;
            $user->password = mb_strtolower(\Illuminate\Support\Str::random(RANDOM_KEY_LENGTH));
            $user->confirmation_code = mb_strtolower(\Illuminate\Support\Str::random(RANDOM_KEY_LENGTH));
            $user->public_id = $lastUser->public_id + 1;
            if (\Illuminate\Support\Facades\Auth::user()->hasFeature(FEATURE_USER_PERMISSIONS)) {
                $user->is_admin = (bool) (\Illuminate\Support\Facades\Request::input('is_admin'));
                $user->permissions = self::formatUserPermissions(\Illuminate\Support\Facades\Request::input('permissions'));
            }
        }

        $user->save();

        if ( ! $user->confirmed && \Illuminate\Support\Facades\Request::input('action') === 'email') {
            $this->userMailer->sendConfirmation($user, \Illuminate\Support\Facades\Auth::user());
            $message = trans('texts.sent_invite');
        } else {
            $message = trans('texts.updated_user');
        }

        \Illuminate\Support\Facades\Session::flash('message', $message);

        return \Illuminate\Support\Facades\Redirect::to('users/' . $user->public_id . '/edit');
    }

    public function sendConfirmation($userPublicId)
    {
        $user = User::where('account_id', '=', \Illuminate\Support\Facades\Auth::user()->account_id)
            ->where('public_id', '=', $userPublicId)->firstOrFail();

        $this->userMailer->sendConfirmation($user, \Illuminate\Support\Facades\Auth::user());
        \Illuminate\Support\Facades\Session::flash('message', trans('texts.sent_invite'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_USER_MANAGEMENT);
    }

    /**
     * Attempt to confirm account with code.
     *
     * @param string $code
     */
    public function confirm($code)
    {
        $user = User::where('confirmation_code', '=', $code)->get()->first();

        if ($user) {
            $notice_msg = trans('texts.security_confirmation');

            $user->confirmed = true;
            $user->confirmation_code = null;
            $user->save();

            if ($user->public_id) {
                \Illuminate\Support\Facades\Auth::logout();
                \Illuminate\Support\Facades\Session::flush();
                $token = \Illuminate\Support\Facades\Password::getRepository()->create($user);

                return \Illuminate\Support\Facades\Redirect::to("/password/reset/{$token}");
            }
            if (\Illuminate\Support\Facades\Auth::check()) {
                if (\Illuminate\Support\Facades\Session::has(REQUESTED_PRO_PLAN)) {
                    \Illuminate\Support\Facades\Session::forget(REQUESTED_PRO_PLAN);
                    $url = '/settings/account_management?upgrade=true';
                } else {
                    $url = '/dashboard';
                }
            } else {
                $url = '/login';
            }

            return \Illuminate\Support\Facades\Redirect::to($url)->with('message', $notice_msg);
        }
        $error_msg = trans('texts.wrong_confirmation');

        return \Illuminate\Support\Facades\Redirect::to('/login')->with('error', $error_msg);
    }

    public function changePassword()
    {
        // check the current password is correct
        if ( ! \Illuminate\Support\Facades\Auth::validate([
            'email'    => \Illuminate\Support\Facades\Auth::user()->email,
            'password' => \Illuminate\Support\Facades\Request::input('current_password'),
        ])) {
            return trans('texts.password_error_incorrect');
        }

        // validate the new password
        $password = \Illuminate\Support\Facades\Request::input('new_password');
        $confirm = \Illuminate\Support\Facades\Request::input('confirm_password');

        if (mb_strlen($password) < 6 || $password != $confirm) {
            return trans('texts.password_error_invalid');
        }

        // save the new password
        $user = \Illuminate\Support\Facades\Auth::user();
        $user->password = bcrypt($password);
        $user->save();

        return RESULT_SUCCESS;
    }

    public function switchAccount($newUserId)
    {
        $oldUserId = \Illuminate\Support\Facades\Auth::user()->id;
        $referer = \Illuminate\Support\Facades\Request::header('referer');
        $account = $this->accountRepo->findUserAccounts($newUserId, $oldUserId);

        if ($account) {
            if ($account->hasUserId($newUserId) && $account->hasUserId($oldUserId)) {
                \Illuminate\Support\Facades\Auth::loginUsingId($newUserId);
                \Illuminate\Support\Facades\Auth::user()->account->loadLocalizationSettings();

                // regenerate token to prevent open pages
                // from saving under the wrong account
                \Illuminate\Support\Facades\Session::put('_token', \Illuminate\Support\Str::random(40));
            }
        }

        // If the user is looking at an entity redirect to the dashboard
        preg_match('/\/[0-9*][\/edit]*$/', $referer, $matches);
        if (count($matches)) {
            return \Illuminate\Support\Facades\Redirect::to('/dashboard');
        }

        return \Illuminate\Support\Facades\Redirect::to($referer);
    }

    public function viewAccountByKey($accountKey)
    {
        $user = $this->accountRepo->findUser(\Illuminate\Support\Facades\Auth::user(), $accountKey);

        if ( ! $user) {
            return redirect()->to('/');
        }

        \Illuminate\Support\Facades\Auth::loginUsingId($user->id);
        \Illuminate\Support\Facades\Auth::user()->account->loadLocalizationSettings();

        $redirectTo = request()->redirect_to ?: '/';

        return redirect()->to($redirectTo);
    }

    public function unlinkAccount($userAccountId, $userId)
    {
        $this->accountRepo->unlinkUser($userAccountId, $userId);
        $referer = \Illuminate\Support\Facades\Request::header('referer');

        $users = $this->accountRepo->loadAccounts(\Illuminate\Support\Facades\Auth::user()->id);
        \Illuminate\Support\Facades\Session::put(SESSION_USER_ACCOUNTS, $users);

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.unlinked_account'));

        return \Illuminate\Support\Facades\Redirect::to('/manage_companies');
    }

    public function manageCompanies()
    {
        return \Illuminate\Support\Facades\View::make('users.account_management');
    }

    public function saveSidebarState(): string
    {
        if (\Illuminate\Support\Facades\Request::has('show_left')) {
            \Illuminate\Support\Facades\Session::put(SESSION_LEFT_SIDEBAR, (bool) (\Illuminate\Support\Facades\Request::input('show_left')));
        }

        if (\Illuminate\Support\Facades\Request::has('show_right')) {
            \Illuminate\Support\Facades\Session::put(SESSION_RIGHT_SIDEBAR, (bool) (\Illuminate\Support\Facades\Request::input('show_right')));
        }

        return RESULT_SUCCESS;
    }

    public function acceptTerms()
    {
        $ip = \Illuminate\Support\Facades\Request::getClientIp();
        $referer = \Illuminate\Support\Facades\Request::server('HTTP_REFERER');
        $message = '';

        if (request()->accepted_terms && request()->accepted_privacy) {
            auth()->user()->acceptLatestTerms($ip)->save();
            $message = trans('texts.accepted_terms');
        }

        return redirect($referer)->withMessage($message);
    }

    private function formatUserPermissions(array $permissions)
    {
        return json_encode(array_diff(array_values($permissions), [0]));
    }
}
