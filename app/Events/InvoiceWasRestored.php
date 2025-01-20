<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasRestored.
 */
class InvoiceWasRestored extends Event
{
    use SerializesModels;

    /**
     * @var Invoice
     */
    public $invoice;

    public $fromDeleted;

    /**
     * Create a new event instance.
     */
    public function __construct(Invoice $invoice, $fromDeleted)
    {
        $this->invoice = $invoice;
        $this->fromDeleted = $fromDeleted;
    }
}
