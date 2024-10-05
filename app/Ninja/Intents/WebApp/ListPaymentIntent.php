<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\BaseIntent;

class ListPaymentIntent extends BaseIntent
{
    public function process(): void
    {
        $this->loadStates(ENTITY_PAYMENT);

        if ($client = $this->requestClient()) {
            $url = $client->present()->url . '#payments';
        } else {
            $url = '/payments';
        }

        return redirect($url);
    }
}
