<?php

namespace App\Http\Controllers\ClientAuth;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;

class PasswordController extends Controller
{
    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param string|null $key
     * @param string|null $token
     *
     * @return Response
     */
    public function getReset(Request $request, $token = null)
    {
        return $this->showResetForm($request, $token);
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param string|null $key
     * @param string|null $token
     *
     * @return Response
     */
    public function showResetForm(Request $request, $token = null)
    {
        if (is_null($token)) {
            return $this->getEmail();
        }

        $data = [
            'token'      => $token,
            'clientauth' => true,
        ];

        if (! session('contact_key')) {
            return Redirect::to('/client/session_expired');
        }

        return view('clientauth.reset')->with($data);
    }

    /**
     * Reset the given user's password.
     *
     *
     * @return Response
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->getResetValidationRules());

        $credentials = $request->only(
            'password',
            'password_confirmation',
            'token'
        );

        $credentials['id'] = null;

        $contactKey = session('contact_key');
        if ($contactKey) {
            $contact = Contact::where('contact_key', '=', $contactKey)->first();
            if ($contact && ! $contact->is_deleted) {
                $credentials['id'] = $contact->id;
            }
        }

        $broker = $this->getBroker();

        $response = Password::broker($broker)->reset($credentials, function ($user, $password): void {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return $this->getResetSuccessResponse($response);

            default:
                return $this->getResetFailureResponse($request, $response);
        }
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array{token: string, password: string}
     */
    protected function getResetValidationRules(): array
    {
        return [
            'token'    => 'required',
            'password' => 'required|confirmed|min:6',
        ];
    }
}
