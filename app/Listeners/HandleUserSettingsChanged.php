<?php

namespace App\Listeners;

use App\Events\UserSettingsChanged;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Class HandleUserSettingsChanged.
 */
class HandleUserSettingsChanged
{
    /**
     * Create the event handler.
     */
    public function __construct(AccountRepository $companyRepo, UserMailer $userMailer)
    {
        $this->accountRepo = $companyRepo;
        $this->userMailer = $userMailer;
    }

    /**
     * Handle the event.
     */
    public function handle(UserSettingsChanged $event): void
    {
        if (! Auth::check()) {
            return;
        }

        $company = Auth::user()->company;
        $company->loadLocalizationSettings();

        $users = $this->accountRepo->loadAccounts(Auth::user()->id);
        Session::put(SESSION_USER_ACCOUNTS, $users);

        if ($event->user && $event->user->confirmed && $event->user->isEmailBeingChanged()) {
            $this->userMailer->sendConfirmation($event->user);
            $this->userMailer->sendEmailChanged($event->user);

            Session::flash('warning', trans('texts.verify_email'));
        }
    }
}
