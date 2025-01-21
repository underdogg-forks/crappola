<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Ninja\Tickets\Actions\TicketOverdue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendOverdueTicketNotification.
 */
class SendOverdueTicketNotification extends Job implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;

    protected Ticket $ticket;

    /**
     * @var string
     */
    protected $server;

    /**
     * Create a new job instance.
     *
     * @param mixed $type
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->server = config('database.default');
    }

    /**
     * Execute the job.
     */
    public function handle(TicketOverdue $ticketOverdue): void
    {
        $ticketOverdue->fire($this->ticket);
    }
}
