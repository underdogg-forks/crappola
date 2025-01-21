<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateClientPortalTicketRequest;
use App\Libraries\Utils;
use App\Models\Ticket;
use App\Ninja\Repositories\TicketRepository;
use App\Services\TicketService;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ClientPortalTicketController extends ClientPortalController
{
    private TicketRepository $ticketRepo;

    private TicketService $ticketService;

    /**
     * ClientPortalTicketController constructor.
     */
    public function __construct(TicketRepository $ticketRepo, TicketService $ticketService)
    {
        $this->ticketRepo = $ticketRepo;
        $this->ticketService = $ticketService;
    }

    /**
     * @return Response
     */
    public function index()
    {
        $contact = $this->getContact();
        if (! $contact) {
            return $this->returnError();
        }
        if (! $contact->company->enable_client_portal) {
            return $this->returnError();
        }

        $company = $contact->company;

        $data = [
            'color'      => $company->primary_color ? $company->primary_color : '#0b4d78',
            'company'    => $company,
            'title'      => trans('texts.tickets'),
            'entityType' => ENTITY_TICKET,
            'columns'    => Utils::trans(['ticket_number', 'subject', 'created_at', 'status']),
            'sortColumn' => 0,
        ];

        return response()->view('public_list', $data);
    }

    /**
     * @param $ticketid
     *
     * @return Factory|View
     */
    public function view($ticketId)
    {
        if (! $contact = $this->getContact()) {
            $this->returnError();
        }

        $company = $contact->company;

        $ticket = Ticket::scope($ticketId, $company->id)
            ->with('comments', 'documents')
            ->first();

        $data['method'] = 'PUT';
        $data['entityType'] = ENTITY_TICKET;

        $data = array_merge($data, self::getViewModel($contact, $ticket));

        return view('tickets.portal.ticket_view', $data);
    }

    /**
     * @return array{color: mixed, ticket: mixed, contact: mixed, company: mixed, title: Translator|string|mixed[]|null, comments: mixed, url: string, datetimeFormat: mixed, company_ticket_settings: mixed}
     */
    private static function getViewModel($contact, $ticket = false): array
    {
        return [
            'color'    => $contact->company->primary_color ? $contact->company->primary_color : '#0b4d78',
            'ticket'   => $ticket,
            'contact'  => $contact,
            'company'  => $contact->company,
            'title'    => $ticket ? trans('texts.ticket') . ' ' . $ticket->ticket_number : trans('texts.new_ticket'),
            'comments' => $ticket ? $ticket->comments() : null,
            'url'      => $ticket ? 'client/tickets/' . $ticket->public_id : 'client/tickets/create',
            //'timezone' => $ticket ? $ticket->company->timezone->name : DEFAULT_TIMEZONE,
            'datetimeFormat'          => $contact->company->getMomentDateTimeFormat(),
            'company_ticket_settings' => $contact->company->company_ticket_settings,
        ];
    }

    /**
     * @return bool
     */
    public function ticketDatatable()
    {
        if (! $contact = $this->getContact()) {
            return false;
        }

        return $this->ticketService->getClientDatatable($contact->client->id);
    }

    /**
     * @return Factory|View
     */
    public function viewTicket($invitationKey)
    {
        if (! $invitation = $this->ticketRepo->findInvitationByKey($invitationKey)) {
            return $this->returnError(trans('texts.ticket_not_found'));
        }

        $company = $invitation->company;
        $ticket = $invitation->ticket;

        $data = [
            'ticket'           => $ticket,
            'company'          => $company,
            'ticketInvitation' => $invitation,
        ];

        return view('invited.ticket', $data);
    }

    public function update(UpdateClientPortalTicketRequest $request)
    {
        $contact = $this->getContact();

        $data = $request->input();

        $data['document_ids'] = $request->document_ids;
        $data['contact_key'] = $contact->contact_key;
        $data['method'] = 'PUT';
        $data['entityType'] = ENTITY_TICKET;
        $data['action'] = TICKET_INBOUND_CONTACT_REPLY;

        $ticket = $this->ticketService->save($data, $request->entity());
        $ticket->load('documents');

        if (! $ticket) {
            $this->returnError();
        }

        $data = array_merge($data, self::getViewModel($contact, $ticket));

        Session::flash('message', trans('texts.updated_ticket'));

        return view('tickets.portal.ticket_view', $data);
    }

    public function create()
    {
        if (! $contact = $this->getContact()) {
            $this->returnError();
        }

        $data['method'] = 'POST';
        $data['entityType'] = ENTITY_TICKET;

        $data = array_merge($data, self::getViewModel($contact));

        return view('tickets.portal.ticket_view', $data);
    }

    public function store(UpdateClientPortalTicketRequest $request)
    {
        if (! $contact = $this->getContact()) {
            $this->returnError();
        }

        $data = $request->input();

        $data['document_ids'] = $request->document_ids;
        $data['contact_key'] = $contact->contact_key;
        $data['action'] = TICKET_CLIENT_NEW;
        $data['is_internal'] = 0;

        $ticket = $this->ticketService->save($data, $request->entity());

        Session::flash('message', trans('texts.updated_ticket'));

        return Redirect::to('/client/tickets/' . $ticket->public_id);
    }
}
