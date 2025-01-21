<?php

namespace App\Ninja\Tickets\Actions;

use App\Models\Ticket;

class TicketMerge extends BaseTicketAction
{
    /**
     * Handle notifications for when tickets are merged.
     */
    public function fire(Ticket $ticket): void
    {
        $handler = new TicketAgentClosed();
        $handler->fire($ticket);
    }
}
