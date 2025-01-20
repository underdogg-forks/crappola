<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteItemsWereUpdated.
 */
class QuoteItemsWereUpdated extends Event
{
    use SerializesModels;

    public $quote;

    /**
     * Create a new event instance.
     */
    public function __construct($quote)
    {
        $this->quote = $quote;
    }
}
