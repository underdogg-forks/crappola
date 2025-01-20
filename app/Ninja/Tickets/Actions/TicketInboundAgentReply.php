<?php

namespace App\Ninja\Tickets\Actions;

use App\Libraries\Utils;
use App\Models\Ticket;
use App\Ninja\Mailers\TicketMailer;
use Log;

/**
 * Class TicketInboundAgentReply.
 */
class TicketInboundAgentReply extends BaseTicketAction
{
    /**
     * Handle a contact reply to an existing ticket.
     */
    public function fire(Ticket $ticket): void
    {
        $company = $ticket->company;
        $companyTicketSettings = $company->company_ticket_settings;

        if ($companyTicketSettings->update_ticket_template_id > 0) {
            $toEmail = $ticket->contact->email;
            $fromEmail = $this->buildFromAddress($companyTicketSettings);
            $fromName = $companyTicketSettings->from_name;
            $subject = trans('texts.ticket_updated_template_subject', ['ticket_number' => $ticket->ticket_number]);

            $view = 'ticket_template';

            $data = [
                'bccEmail'   => $companyTicketSettings->alert_new_comment_id_email,
                'body'       => parent::buildTicketBodyResponse($ticket, $companyTicketSettings, $companyTicketSettings->update_ticket_template_id),
                'company'    => $company,
                'replyTo'    => $ticket->getTicketEmailFormat(),
                'invitation' => $ticket->invitations->first(),
            ];

            $ticketMailer = new TicketMailer();

            $msg = $ticketMailer->sendTo($toEmail, $fromEmail, $fromName, $subject, $view, $data);

            if (Utils::isSelfHost() && config('app.debug')) {
                Log::info("Sending email - To: {$toEmail} | Reply: {$ticket->getTicketEmailFormat()} | From: {$fromEmail}");
                Log::error($msg);
            }
        }
    }
}
