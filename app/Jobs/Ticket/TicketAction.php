<?php

namespace App\Jobs\Ticket;

use App\Jobs\Job;
use App\Models\Ticket;
use App\Ninja\Tickets\Factory\TicketFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class TicketAction.
 *
 * Queues the processing of ticket actions's
 */
class TicketAction extends Job implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;

    /**
     * @var Ticket_attributes
     */
    protected $deltaAttributes;

    /**
     * @var Ticket
     */
    protected $originalTicket;

    /**
     * @var Ticket
     */
    protected $updatedTicket;

    /**
     * @var mixed
     */
    protected $server;

    /**
     * @var mixed
     */
    protected $action;

    /**
     * TicketAction constructor.
     *
     * @param array $deltaAttributes
     * @param array $originalTicket
     */
    public function __construct($deltaAttributes, $originalTicket, $updatedTicket, $action)
    {
        $this->deltaAttributes = $deltaAttributes;
        $this->originalTicket = $originalTicket;
        $this->updatedTicket = $updatedTicket;
        $this->server = config('database.default');
        $this->action = $action;
    }

    /**
     * process action.
     */
    public function handle(): void
    {
        $ticketHandler = new TicketFactory($this->originalTicket, $this->deltaAttributes, $this->updatedTicket, $this->action);
        $ticketHandler->process();
    }
}
