<?php

namespace App\Jobs\Ticket;

use App\Jobs\Job;
use App\Models\Ticket;
use App\Ninja\Mailers\TicketMailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class TicketSendNotificationEmail.
 */
class TicketSendNotificationEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue;
    use SerializesModels;

    protected Ticket $ticket;

    protected array $ticketData;

    /**
     * @var mixed
     */
    protected $server;

    /**
     * TicketSendNotificationEmail constructor.
     */
    public function __construct(array $ticketData, Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->ticketData = $ticketData;
        $this->server = config('database.default');
    }

    public function handle(TicketMailer $mailer): void
    {
        //harvest list of contacts to email;
        $data['bccEmail'] = $this->ticket->getCCs();
        $data['text'] = $this->ticketData['comment'];
        $data['replyTo'] = 'ticket-123@support.invoiceninja.com';
        //$toEmail = strtolower($this->ticket->contact->email); //todo
        $toEmail = 'david@romulus.com.au';
        $fromEmail = $this->ticket->getTicketFromEmail();
        $fromName = $this->ticket->getTicketFromName();
        $subject = $this->ticket->subject;
        $view = 'ticket';

        $mailer->sendTo($toEmail, $fromEmail, $fromName, $subject, $view, $data);
    }
}
