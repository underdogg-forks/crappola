<?php

namespace App\Ninja\Intents\WebApp;

use App\Ninja\Intents\InvoiceIntent;

class ListInvoiceIntent extends InvoiceIntent
{
    public function process(): string|bool
    {
        $this->loadStates(ENTITY_INVOICE);
        $this->loadStatuses(ENTITY_INVOICE);

        $url = ($client = $this->requestClient()) ? $client->present()->url . '#invoices' : '/invoices';

        return redirect($url);
    }
}
