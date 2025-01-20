<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

/**
 * Class SubscriptionController.
 */
class SubscriptionController extends BaseController
{
    protected SubscriptionService $subscriptionService;

    /**
     * SubscriptionController constructor.
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        //parent::__construct();

        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @return RedirectResponse
     */
    public function index()
    {
        return Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }

    public function getDatatable()
    {
        return $this->subscriptionService->getDatatable(Auth::user()->company_id);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($publicId)
    {
        $subscription = Subscription::scope($publicId)->firstOrFail();

        $data = [
            'subscription' => $subscription,
            'method'       => 'PUT',
            'url'          => 'subscriptions/' . $publicId,
            'title'        => trans('texts.edit_subscription'),
        ];

        return View::make('companies.subscription', $data);
    }

    /**
     * @return RedirectResponse
     */
    public function update($publicId)
    {
        return $this->save($publicId);
    }

    /**
     * @param bool $subscriptionPublicId
     *
     * @return $this|RedirectResponse
     */
    public function save($subscriptionPublicId = false)
    {
        if (Auth::user()->company->hasFeature(FEATURE_API)) {
            $rules = [
                'event_id'   => 'required',
                'target_url' => 'required|url',
            ];

            if ($subscriptionPublicId) {
                $subscription = Subscription::scope($subscriptionPublicId)->firstOrFail();
            } else {
                $subscription = Subscription::createNew();
                $subscriptionPublicId = $subscription->public_id;
            }

            $validator = Validator::make(\Request::all(), $rules);

            if ($validator->fails()) {
                return Redirect::to($subscriptionPublicId ? 'subscriptions/edit' : 'subscriptions/create')->withInput()->withErrors($validator);
            }

            $subscription->fill(request()->all());
            $subscription->save();

            $message = $subscriptionPublicId ? trans('texts.updated_subscription') : trans('texts.created_subscription');

            Session::flash('message', $message);
        }

        return redirect('/settings/api_tokens');

        /*
        if ($subscriptionPublicId) {
            return Redirect::to('subscriptions/' . $subscriptionPublicId . '/edit');
        } else {
            return redirect('/settings/api_tokens');
        }
        */
    }

    /**
     * @return RedirectResponse
     */
    public function store()
    {
        return $this->save();
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $data = [
            'subscription' => null,
            'method'       => 'POST',
            'url'          => 'subscriptions',
            'title'        => trans('texts.add_subscription'),
        ];

        return View::make('companies.subscription', $data);
    }

    /**
     * @return RedirectResponse
     */
    public function bulk()
    {
        $action = $request->get('bulk_action');
        $ids = $request->get('bulk_public_id');

        $count = $this->subscriptionService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_subscription'));

        return Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }
}
