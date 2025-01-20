<?php

namespace App\Ninja\Tickets\Actions;

use App\Libraries\Utils;
use App\Models\Ticket;
use App\Ninja\Mailers\TicketMailer;
use Log;

/**
 * Class TicketInboundContactReply.
 */
class TicketInboundContactReply extends BaseTicketAction
{
    /**
     * Handle a contact reply to an existing ticket.
     */

    /**
     * Fire sequence for INBOUND_CONTACT_REPLY.
     */
    public function fire(Ticket $ticket): void
    {
        $company = $ticket->company;
        $companyTicketSettings = $company->company_ticket_settings;

        if ($companyTicketSettings->alert_new_comment_id > 0 && $ticket->agent_id > 0) {
            $toEmail = $ticket->agent->email;
            $fromEmail = $this->buildFromAddress($companyTicketSettings);
            $fromName = $companyTicketSettings->from_name;
            $subject = trans('texts.ticket_contact_reply', ['ticket_number' => $ticket->ticket_number, 'contact' => $ticket->getContactName()]);
            $view = 'ticket_template';

            $data = [
                'bccEmail'   => $companyTicketSettings->alert_new_comment_id_email,
                'body'       => parent::buildTicketBodyResponse($ticket, $companyTicketSettings, $companyTicketSettings->alert_new_comment_id),
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
