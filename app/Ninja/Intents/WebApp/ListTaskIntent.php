<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\BaseIntent;

class ListTaskIntent extends BaseIntent
{
    public function process(): string|bool
    {
        $this->loadStates(ENTITY_TASK);

        $url = ($client = $this->requestClient()) ? $client->present()->url . '#tasks' : '/tasks';

        return redirect($url);
    }
}
