<?php

namespace App\Http\Controllers\ClientAuth;

use App\Http\Controllers\Controller;
use App\Libraries\Utils;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Traits\SendsEmails;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    /*
            |--------------------------------------------------------------------------
            | Password Reset Controller
            |--------------------------------------------------------------------------
            |
            | This controller is responsible for handling password reset emails and
            | includes a trait which assists in sending these notifications from
            | your application to your users. Feel free to explore this trait.
            |
            */

    use SendsEmails;
    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:client');

        //Config::set('auth.defaults.passwords', 'client');
    }

    /**
     * @return Factory|Application|RedirectResponse|View
     */
    public function showLinkRequestForm()
    {
        $data = [
            'clientauth' => true,
        ];

        return view('clientauth.passwords.email')->with($data);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param Request $request
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        // resolve the email to a contact/account
        $account = false;
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

        if ( ! $account || ! request()->email) {
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }

        $contact = Contact::where('email', '=', request()->email)
            ->where('account_id', '=', $account->id)
            ->first();

        if ($contact) {
            $contactId = $contact->id;
        } else {
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }

        $response = $this->broker()->sendResetLink(['id' => $contactId], function (Message $message): void {
            $message->subject($this->getEmailSubject());
        });

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    protected function broker()
    {
        return Password::broker('clients');
    }
}
