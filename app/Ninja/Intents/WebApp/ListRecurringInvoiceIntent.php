<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\BaseIntent;

class ListRecurringInvoiceIntent extends BaseIntent
{
    public function process(): void
    {
        $this->loadStates(ENTITY_RECURRING_INVOICE);

        $url = ($client = $this->requestClient()) ? $client->present()->url . '#recurring_invoices' : '/recurring_invoices';

        return redirect($url);
    }
}
