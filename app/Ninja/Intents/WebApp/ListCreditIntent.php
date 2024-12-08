<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\BaseIntent;

class ListCreditIntent extends BaseIntent
{
    public function process(): string|bool
    {
        $this->loadStates(ENTITY_CREDIT);

        $url = ($client = $this->requestClient()) ? $client->present()->url . '#credits' : '/credits';

        return redirect($url);
    }
}
