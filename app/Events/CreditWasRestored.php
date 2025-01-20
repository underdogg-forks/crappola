<?php

namespace App\Events;

use App\Models\Credit;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreditWasRestored.
 */
class CreditWasRestored extends Event
{
    use SerializesModels;

    /**
     * @var Credit
     */
    public $credit;

    /**
     * Create a new event instance.
     */
    public function __construct(Credit $credit)
    {
        $this->credit = $credit;
    }
}
