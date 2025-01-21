<?php

namespace App\Ninja\Tickets\Actions;

use App\Constants\Domain;
use App\Libraries\Utils;
use App\Models\AccountTicketSettings;
use App\Models\Ticket;
use App\Ninja\Mailers\TicketMailer;
use App\Services\TicketTemplateService;
use Log;

/**
 * Class BaseTicketAction.
 */
class BaseTicketAction
{
    /**
     * fires new_ticket_template to client.
     */
    public function newTicketTemplateAction(Ticket $ticket): void
    {
        $company = $ticket->company;
        $companyTicketSettings = $company->company_ticket_settings;

        if ($companyTicketSettings->new_ticket_template_id > 0) {
            $toEmail = $ticket->contact->email;
            $fromEmail = $this->buildFromAddress($companyTicketSettings);
            $fromName = $companyTicketSettings->from_name;
            $subject = trans('texts.ticket_new_template_subject', ['ticket_number' => $ticket->ticket_number]);
            $view = 'ticket_template';

            $data = [
                'body'       => self::buildTicketBodyResponse($ticket, $companyTicketSettings, $companyTicketSettings->new_ticket_template_id),
                'company'    => $company,
                'replyTo'    => $ticket->getTicketEmailFormat(),
                'invitation' => $ticket->invitations->first(),
            ];

            $ticketMailer = new TicketMailer();

            $msg = $ticketMailer->sendTo($toEmail, $fromEmail, $fromName, $subject, $view, $data);

            if (Utils::isSelfHost() && config('app.debug')) {
                Log::info("Sending email - To: {$toEmail} | Reply: {$fromEmail} | From: {$subject}");
                Log::info($msg);
            }
        }
    }

    /**
     * Builds the company support email address.
     */
    public function buildFromAddress(AccountTicketSettings $companyTicketSettings): string
    {
        $fromName = $companyTicketSettings->support_email_local_part;

        if (Utils::isNinjaProd()) {
            $domainName = Domain::getSupportDomainFromId($companyTicketSettings->company->domain_id);
        } else {
            $domainName = config('ninja.tickets.ticket_support_domain');
        }

        return "{$fromName}@{$domainName}";
    }

    public static function buildTicketBodyResponse(Ticket $ticket, $companyTicketSettings, $templateId): string
    {
        $ticketVariables = TicketTemplateService::getVariables($ticket);
        $template = $ticket->getTicketTemplate($templateId);
        $ticketVariables = array_merge(
            $ticketVariables,
            [
                'ticket_master' => $companyTicketSettings->ticket_master->getName(),
            ]
        );

        return str_replace(array_keys($ticketVariables), array_values($ticketVariables), $template->description);
    }

    /**
     * Sets the default agent to a ticket if exists.
     */
    public function setDefaultAgent($ticket, $companyTicketSettings): void
    {
        if ($companyTicketSettings->default_agent_id > 0) {
            $ticket->agent_id = $companyTicketSettings->default_agent_id;
            $ticket->save();
        }
    }
}
