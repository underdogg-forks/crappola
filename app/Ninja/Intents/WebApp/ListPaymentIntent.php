<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\BaseIntent;

class ListPaymentIntent extends BaseIntent
{
    public function process(): void
    {
        $this->loadStates(ENTITY_PAYMENT);

        $url = ($client = $this->requestClient()) ? $client->present()->url . '#payments' : '/payments';

        return redirect($url);
    }
}
