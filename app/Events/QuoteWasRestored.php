<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class QuoteWasRestored.
 */
class QuoteWasRestored extends Event
{
    use SerializesModels;

    public $quote;

    /**
     * Create a new event instance.
     *
     * @param $quote
     */
    public function __construct($quote)
    {
        $this->quote = $quote;
    }
}
