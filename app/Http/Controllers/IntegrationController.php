<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Utils;

/**
 * Class IntegrationController.
 */
class IntegrationController extends BaseAPIController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe()
    {
        $eventId = Utils::lookupEventId(trim(\Illuminate\Support\Facades\Request::input('event')));

        if ( ! $eventId) {
            return \Illuminate\Support\Facades\Response::json('Event is invalid', 500);
        }

        $subscription = Subscription::createNew();
        $subscription->event_id = $eventId;
        $subscription->target_url = trim(\Illuminate\Support\Facades\Request::input('target_url'));
        $subscription->save();

        if ( ! $subscription->id) {
            return \Illuminate\Support\Facades\Response::json('Failed to create subscription', 500);
        }

        return \Illuminate\Support\Facades\Response::json(['id' => $subscription->public_id], 201);
    }

    public function unsubscribe($publicId)
    {
        $subscription = Subscription::scope($publicId)->firstOrFail();
        $subscription->delete();

        return $this->response(RESULT_SUCCESS);
    }
}
