<?php

namespace App\Http\Controllers;

use App\Libraries\Utils;
use App\Models\Subscription;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

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
        $eventId = Utils::lookupEventId(trim(Request::input('event')));

        if ( ! $eventId) {
            return Response::json('Event is invalid', 500);
        }

        $subscription = Subscription::createNew();
        $subscription->event_id = $eventId;
        $subscription->target_url = trim(Request::input('target_url'));
        $subscription->save();

        if ( ! $subscription->id) {
            return Response::json('Failed to create subscription', 500);
        }

        return Response::json(['id' => $subscription->public_id], 201);
    }

    public function unsubscribe($publicId)
    {
        $subscription = Subscription::scope($publicId)->firstOrFail();
        $subscription->delete();

        return $this->response(RESULT_SUCCESS);
    }
}
