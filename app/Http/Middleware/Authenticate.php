<?php

namespace App\Http\Middleware;

use App\Libraries\Utils;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Invitation;
use App\Models\ProposalInvitation;
use App\Models\TicketInvitation;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

/**
 * Class Authenticate.
 */
class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param string  $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'user')
    {
        $authenticated = Auth::guard($guard)->check();

        $invitationKey = false;

        if ($request->invitation_key) {
            $invitationKey = $request->invitation_key;
        } elseif ($request->proposal_invitation_key) {
            $invitationKey = $request->proposal_invitation_key;
        } elseif ($request->ticket_invitation_key) {
            $invitationKey = $request->ticket_invitation_key;
        }

        if ($guard == 'client') {
            if (! empty($request->invitation_key) || ! empty($request->proposal_invitation_key) || ! empty($request->ticket_invitation_key)) {
                $contact_key = session('contact_key');
                if ($contact_key) {
                    $contact = $this->getContact($contact_key);
                    $invitation = $this->getInvitation($invitationKey, ! empty($request->proposal_invitation_key), ! empty($request->ticket_invitation_key));

                    if (! $invitation) {
                        return response()->view('error', [
                            'error'      => trans('texts.invoice_not_found'),
                            'hideHeader' => true,
                        ]);
                    }

                    if ($contact && $contact->id != $invitation->contact_id) {
                        // This is a different client; reauthenticate
                        $authenticated = false;
                        Auth::guard($guard)->logout();
                    }
                    Session::put('contact_key', $invitation->contact->contact_key);
                }
            }

            if (! empty($request->contact_key)) {
                $contact_key = $request->contact_key;
                Session::put('contact_key', $contact_key);
            } else {
                $contact_key = session('contact_key');
            }

            $contact = false;
            if ($contact_key) {
                $contact = $this->getContact($contact_key);
            } elseif ($invitationKey && $invitation = $this->getInvitation($invitationKey, ! empty($request->proposal_invitation_key), ! empty($request->ticket_invitation_key))) {
                $contact = $invitation->contact;
                Session::put('contact_key', $contact->contact_key);
            }
            if (! $contact) {
                return Redirect::to('client/session_expired');
            }

            $company = $contact->company;

            if (Auth::guard('user')->check() && Auth::user('user')->company_id == $company->id) {
                // This is an admin; let them pretend to be a client
                $authenticated = true;
            }

            // Does this company require portal passwords?
            if ($company && (! $company->enable_portal_password || ! $company->hasFeature(FEATURE_CLIENT_PORTAL_PASSWORD))) {
                $authenticated = true;
            }

            if (! $authenticated && $contact && ! $contact->password) {
                $authenticated = true;
            }

            if (env('PHANTOMJS_SECRET') && $request->phantomjs_secret && hash_equals(env('PHANTOMJS_SECRET'), $request->phantomjs_secret)) {
                $authenticated = true;
            }

            if ($authenticated) {
                $request->merge(['contact' => $contact]);
                $company->loadLocalizationSettings($contact->client);
            }
        }

        if (! $authenticated) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            }
            if ($guard == 'client') {
                $url = '/client/login';
                if (Utils::isNinjaProd()) {
                    if ($company && Utils::getSubdomain() == 'app') {
                        $url .= '?account_key=' . $company->account_key;
                    }
                } else {
                    if ($company && Company::count() > 1) {
                        $url .= '?account_key=' . $company->account_key;
                    }
                }
            } else {
                $url = '/login';
            }

            return redirect()->guest($url);
        }

        return $next($request);
    }

    /**
     * @return Model|null|static
     */
    protected function getContact($key)
    {
        $contact = Contact::withTrashed()->where('contact_key', '=', $key)->first();
        if (! $contact) {
            return;
        }
        if ($contact->is_deleted) {
            return;
        }

        return $contact;
    }

    /**
     * @return Model|null|static
     */
    protected function getInvitation($key, $isProposal = false, $isTicket = false)
    {
        if (! $key) {
            return false;
        }

        // check for extra params at end of value (from website feature)
        [$key] = explode('&', $key);
        $key = substr($key, 0, RANDOM_KEY_LENGTH);

        if ($isProposal) {
            $invitation = ProposalInvitation::withTrashed()->where('invitation_key', '=', $key)->first();
        } elseif ($isTicket) {
            $invitation = TicketInvitation::withTrashed()->where('invitation_key', '=', $key)->first();
        } else {
            $invitation = Invitation::withTrashed()->where('invitation_key', '=', $key)->first();
        }
        if (! $invitation) {
            return;
        }
        if ($invitation->is_deleted) {
            return;
        }

        return $invitation;
    }
}
