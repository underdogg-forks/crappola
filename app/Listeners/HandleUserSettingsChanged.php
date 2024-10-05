<?php

namespace App\Listeners;

use App\Events\UserSettingsChanged;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;

/**
 * Class HandleUserSettingsChanged.
 */
class HandleUserSettingsChanged
{
    /**
     * Create the event handler.
     *
     * @param AccountRepository $accountRepo
     * @param UserMailer        $userMailer
     */
    public function __construct(AccountRepository $accountRepo, UserMailer $userMailer)
    {
        $this->accountRepo = $accountRepo;
        $this->userMailer = $userMailer;
    }

    /**
     * Handle the event.
     *
     * @param UserSettingsChanged $event
     *
     * @return void
     */
    public function handle(UserSettingsChanged $event): void
    {
        if ( ! \Illuminate\Support\Facades\Auth::check()) {
            return;
        }

        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $account->loadLocalizationSettings();

        $users = $this->accountRepo->loadAccounts(\Illuminate\Support\Facades\Auth::user()->id);
        \Illuminate\Support\Facades\Session::put(SESSION_USER_ACCOUNTS, $users);

        if ($event->user && $event->user->confirmed && $event->user->isEmailBeingChanged()) {
            $this->userMailer->sendConfirmation($event->user);
            $this->userMailer->sendEmailChanged($event->user);

            \Illuminate\Support\Facades\Session::flash('warning', trans('texts.verify_email'));
        }
    }
}
