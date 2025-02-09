<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Libraries\Utils;
use App\Models\Account;
use App\Ninja\Mailers\Mailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Redirect;
use Request;
use Session;

/**
 * Class HomeController.
 */
class HomeController extends BaseController
{
    protected Mailer $mailer;

    /**
     * HomeController constructor.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        //parent::__construct();

        $this->mailer = $mailer;
    }

    /**
     * @return RedirectResponse
     */
    public function showIndex()
    {
        \Illuminate\Support\Facades\Session::reflash();

        if ( ! Utils::isNinja() && ( ! Utils::isDatabaseSetup() || Account::count() == 0)) {
            return \Illuminate\Support\Facades\Redirect::to('/setup');
        }

        if (Auth::check()) {
            return \Illuminate\Support\Facades\Redirect::to('/dashboard');
        }

        return \Illuminate\Support\Facades\Redirect::to('/login');
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function viewLogo()
    {
        return View::make('public.logo');
    }

    /**
     * @return \Illuminate\Contracts\View\View|RedirectResponse
     */
    public function invoiceNow()
    {
        $url = 'https://invoicing.co';

        if (\Illuminate\Support\Facades\Request::has('rc')) {
            $url = $url . '?rc=' . \Illuminate\Support\Facades\Request::input('rc');
        }

        return \Illuminate\Support\Facades\Redirect::to($url);

        /*
        // Track the referral/campaign code
        if (Request::has('rc')) {
            session([SESSION_REFERRAL_CODE => \Request::input('rc')]);
        }

        if (Auth::check()) {
            $redirectTo = \Request::input('redirect_to') ? SITE_URL . '/' . ltrim(\Request::input('redirect_to'), '/') : 'invoices/create';
            return Redirect::to($redirectTo)->with('sign_up', \Request::input('sign_up'));
        } else {
            return View::make('public.invoice_now');
        }
        */
    }

    /**
     * @param $userType
     * @param $version
     *
     * @return JsonResponse
     */
    public function newsFeed($userType, $version)
    {
        $response = Utils::getNewsFeedResponse($userType);

        return Response::json($response);
    }

    public function hideMessage(): string
    {
        if (Auth::check() && \Illuminate\Support\Facades\Session::has('news_feed_id')) {
            $newsFeedId = \Illuminate\Support\Facades\Session::get('news_feed_id');
            if ($newsFeedId != NEW_VERSION_AVAILABLE && $newsFeedId > Auth::user()->news_feed_id) {
                $user = Auth::user();
                $user->news_feed_id = $newsFeedId;
                $user->save();
            }
        }

        \Illuminate\Support\Facades\Session::forget('news_feed_message');

        return 'success';
    }

    public function logError()
    {
        return Utils::logError(\Illuminate\Support\Facades\Request::input('error'), 'JavaScript');
    }

    public function keepAlive(): string
    {
        return RESULT_SUCCESS;
    }

    public function loggedIn(): string
    {
        return RESULT_SUCCESS;
    }

    public function contactUs(): string
    {
        $message = request()->contact_us_message;

        if (request()->include_errors) {
            $message .= "\n\n" . implode("\n", Utils::getErrors());
        }

        Mail::raw($message, function ($message): void {
            $subject = 'Customer Message [';
            if (Utils::isNinjaProd()) {
                $subject .= str_replace('db-ninja-', '', config('database.default'));
                $subject .= Auth::user()->present()->statusCode . '] ';
            } else {
                $subject .= 'Self-Host] | ';
            }

            $subject .= date('M jS, g:ia');
            $message->to(env('CONTACT_EMAIL', 'contact@invoiceninja.com'))
                ->from(CONTACT_EMAIL, Auth::user()->present()->fullName)
                ->replyTo(Auth::user()->email, Auth::user()->present()->fullName)
                ->subject($subject);
        });

        return RESULT_SUCCESS;
    }
}
