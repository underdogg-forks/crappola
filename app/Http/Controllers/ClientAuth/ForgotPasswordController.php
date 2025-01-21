<?php

namespace App\Http\Controllers\ClientAuth;

use App\Http\Controllers\Controller;
use App\Libraries\Utils;
use App\Models\Company;
use App\Models\Contact;
use Config;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Password;

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
     * @return RedirectResponse
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
     *
     * @return Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        // resolve the email to a contact/company
        $company = false;
        if (! Utils::isNinja() && Company::count() == 1) {
            $company = Company::first();
        } elseif ($companyKey = request()->account_key) {
            $company = Company::whereAccountKey($companyKey)->first();
        } else {
            $subdomain = Utils::getSubdomain(\Request::server('HTTP_HOST'));
            if ($subdomain && $subdomain != 'app') {
                $company = Company::whereSubdomain($subdomain)->first();
            }
        }
        if (! $company) {
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }
        if (! request()->email) {
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }

        $contact = Contact::where('email', '=', request()->email)
            ->where('company_id', '=', $company->id)
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
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    protected function broker()
    {
        return Password::broker('clients');
    }
}
