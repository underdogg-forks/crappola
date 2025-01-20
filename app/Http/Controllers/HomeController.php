<?php

namespace App\Http\Controllers;

use App\Libraries\Utils;
use App\Models\Company;
use App\Ninja\Mailers\Mailer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Input;
use Mail;
use Redirect;
use Response;
use Session;
use View;

/**
 * Class HomeController.
 */
class HomeController extends BaseController
{
    /**
     * @var Mailer
     */
    protected $mailer;

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
        Session::reflash();

        if (!Utils::isNinja() && (!Utils::isDatabaseSetup() || company::count() == 0)) {
            return Redirect::to('/setup');
        } elseif (Auth::check()) {
            return Redirect::to('/dashboard');
        }

        return Redirect::to('/login');
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
        // Track the referral/campaign code
        if (request()->has('rc')) {
            session([SESSION_REFERRAL_CODE => $request->get('rc')]);
        }

        if (Auth::check()) {
            $redirectTo = $request->get('redirect_to') ? SITE_URL . '/' . ltrim($request->get('redirect_to'), '/') : 'invoices/create';

            return Redirect::to($redirectTo)->with('sign_up', $request->get('sign_up'));
        }

        return View::make('public.invoice_now');
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

    /**
     * @return string
     */
    public function hideMessage()
    {
        if (Auth::check() && Session::has('news_feed_id')) {
            $newsFeedId = Session::get('news_feed_id');
            if ($newsFeedId != NEW_VERSION_AVAILABLE && $newsFeedId > Auth::user()->news_feed_id) {
                $user = Auth::user();
                $user->news_feed_id = $newsFeedId;
                $user->save();
            }
        }

        Session::forget('news_feed_message');

        return 'success';
    }

    /**
     * @return string
     */
    public function logError()
    {
        return Utils::logError(request()->get('error'), 'JavaScript');
    }

    /**
     * @return mixed
     */
    public function keepAlive()
    {
        return RESULT_SUCCESS;
    }

    /**
     * @return mixed
     */
    public function loggedIn()
    {
        return RESULT_SUCCESS;
    }

    /**
     * @return mixed
     */
    public function contactUs()
    {
        $message = request()->contact_us_message;

        if (request()->include_errors) {
            $message .= "\n\n" . join("\n", Utils::getErrors());
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
