<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\BaseIntent;

class ListClientIntent extends BaseIntent
{
    public function process(): string|bool
    {
        $this->loadStates(ENTITY_CLIENT);

        return redirect('/clients');
    }
}
