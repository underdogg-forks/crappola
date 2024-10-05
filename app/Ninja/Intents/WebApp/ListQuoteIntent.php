<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\InvoiceIntent;

class ListQuoteIntent extends InvoiceIntent
{
    public function process(): void
    {
        $this->loadStates(ENTITY_QUOTE);
        $this->loadStatuses(ENTITY_QUOTE);

        $url = ($client = $this->requestClient()) ? $client->present()->url . '#quotes' : '/quotes';

        return redirect($url);
    }
}
