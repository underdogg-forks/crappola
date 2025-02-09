<?php

namespace App\Listeners;

use App\Events\UserSignedUp;
use App\Libraries\Utils;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class HandleUserSignedUp.
 */
class HandleUserSignedUp
{
    protected AccountRepository $accountRepo;

    protected UserMailer $userMailer;

    /**
     * Create the event handler.
     */
    public function __construct(AccountRepository $accountRepo, UserMailer $userMailer)
    {
        $this->accountRepo = $accountRepo;
        $this->userMailer = $userMailer;
    }

    /**
     * Handle the event.
     *
     *
     */
    public function handle(UserSignedUp $event): void
    {
        $user = Auth::user();

        if (Utils::isNinjaProd() && ! $user->confirmed) {
            $this->userMailer->sendConfirmation($user);
        } elseif (Utils::isNinjaDev()) {
            // do nothing
        } else {
            $this->accountRepo->registerNinjaUser($user);
        }

        session([SESSION_COUNTER => -1]);
        session([SESSION_DB_SERVER => config('database.default')]);
    }
}
