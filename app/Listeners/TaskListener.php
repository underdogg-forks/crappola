<?php

namespace App\Listeners;

use App\Events\InvoiceWasDeleted;
use App\Models\Task;

/**
 * Class TaskListener.
 */
class TaskListener
{
    public function deletedInvoice(InvoiceWasDeleted $event): void
    {
        // Release any tasks associated with the deleted invoice
        Task::where('invoice_id', '=', $event->invoice->id)
            ->update(['invoice_id' => null]);
    }
}
