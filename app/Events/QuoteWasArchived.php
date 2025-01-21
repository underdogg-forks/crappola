<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class QuoteWasArchived extends Event
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
