<?php

namespace App\Events;

use App\Models\Invitation;
use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;

class QuoteInvitationWasApproved extends Event
{
    use SerializesModels;

    /**
     * @var Invoice
     */
    public $quote;

    /**
     * @var Invitation
     */
    public $invitation;

    /**
     * Create a new event instance.
     *
     * @param Invoice $invoice
     */
    public function __construct(Invoice $quote, Invitation $invitation)
    {
        $this->quote = $quote;
        $this->invitation = $invitation;
    }
}
