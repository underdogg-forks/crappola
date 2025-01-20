<?php

namespace App\Http\Controllers;

use App\Events\TicketUserViewed;
use App\Http\Requests\CreateTicketRequest;
use App\Http\Requests\TicketAddEntityRequest;
use App\Http\Requests\TicketInboundRequest;
use App\Http\Requests\TicketMergeRequest;
use App\Http\Requests\TicketRemoveEntityRequest;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Libraries\Utils;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketRelation;
use App\Models\User;
use App\Ninja\Datatables\TicketDatatable;
use App\Ninja\Repositories\TicketRepository;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

/**
 * Class TicketController.
 */
class TicketController extends BaseController
{
    protected TicketService $ticketService;

    protected $ticketRepository;

    /**
     * TicketController constructor.
     */
    public function __construct(TicketService $ticketService, TicketRepository $ticketRepository)
    {
        $this->ticketService = $ticketService;
        $this->ticketRepo = $ticketRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_TICKET,
            'datatable'  => new TicketDatatable(),
            'title'      => trans('texts.tickets'),
        ]);
    }

    public function getDatatable($clientPublicId = null)
    {
        $search = $request->get('sSearch');

        return $this->ticketService->getDatatable($search);
    }

    /**
     * @return Redirect
     */
    public function show($publicId)
    {
        Session::reflash();

        return redirect("tickets/$publicId/edit");
    }

    /**
     * @return View
     */
    public function edit(TicketRequest $request)
    {
        $ticket = $request->entity();

        $clients = false;

        //If we are missing a client from the ticket, load clients for assignment
        if ($ticket->is_internal == true && ! $ticket->client_id) {
            $clients = Client::scope()->with('contacts')->get();
        } elseif (! $ticket->client_id) {
            $clients = $this->ticketService->findClientsByContactEmail($ticket->contact_key);
        }

        $data = array_merge(self::getViewModel($ticket, $clients));

        event(new TicketUserViewed($ticket));

        return View::make('tickets.edit', $data);
    }

    private static function getViewModel($ticket = false, $clients = false): array
    {
        return [
            'clients' => $clients,
            //'status' => $ticket->status(),
            'comments'       => $ticket->comments(),
            'company'        => Auth::user()->company,
            'url'            => 'tickets/' . $ticket->public_id,
            'ticket'         => $ticket,
            'entity'         => $ticket,
            'title'          => trans('texts.edit_ticket'),
            'timezone'       => Auth::user()->company->timezone ? Auth::user()->company->timezone->name : DEFAULT_TIMEZONE,
            'datetimeFormat' => Auth::user()->company->getMomentDateTimeFormat(),
            'method'         => 'PUT',
            'isAdminUser'    => Auth::user()->is_admin || Auth::user()->isTicketMaster() ? true : false,
        ];
    }

    /**
     * @return View
     */
    public function update(UpdateTicketRequest $request)
    {
        $data = $request->input();
        $data['document_ids'] = $request->document_ids;

        if ($data['closed'] != '0000-00-00 00:00:00') {
            $data['action'] = TICKET_AGENT_CLOSED;
        } elseif (isset($data['description']) && strlen($data['description']) > 0) {
            $data['action'] = TICKET_AGENT_UPDATE;
        } else {
            $data['action'] = TICKET_SAVE_ONLY;
        }

        $ticket = $request->entity();
        $ticket = $this->ticketService->save($data, $ticket);

        $ticket->load('documents', 'relations');

        $entityType = $ticket->getEntityType();

        $message = trans("texts.updated_{$entityType}");

        Session::flash('message', $message);

        $data = array_merge($this->getViewmodel($ticket), $data);

        return View::make('tickets.edit', $data);
    }

    /**
     * @return RedirectResponse|Redirector
     */
    public function bulk()
    {
        $action = $request->get('action');

        $ids = $request->get('public_id') ? $request->get('public_id') : $request->get('ids');

        if ($action == 'purge' && ! auth()->user()->is_admin) {
            return redirect('dashboard')->withError(trans('texts.not_authorized'));
        }

        $count = $this->ticketService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_ticket', $count);

        Session::flash('message', $message);

        if ($action == 'purge') {
            return redirect('dashboard')->withMessage($message);
        }

        return $this->returnBulk(ENTITY_TICKET, $action, $ids);
    }

    /**
     * @param int $parentTicketId
     *
     * @return View
     */
    public function create(TicketRequest $request, $parentTicketId = 0)
    {
        $parentTicket = Ticket::scope($parentTicketId)->first();

        $parentTicketClientExists = false;

        if ($parentTicket && method_exists($parentTicket, 'client')) {
            $parentTicket->load('client');
            $parentTicketClientExists = true;
        }

        //need to mock a ticket object or check if $request->old() exists and pass that in its place.
        $mockTicket = [
            'parent_ticket_id' => $parentTicketId ? $parentTicketId : null,
            'subject'          => '',
            'description'      => '',
            'due_at'           => '',
            'client_public_id' => $parentTicketClientExists ? $parentTicket->client->public_id : null,
            'agent_id'         => null,
            'is_internal'      => $parentTicketClientExists ? true : false,
            'private_notes'    => '',
            'priority_id'      => 1,
        ];

        $data = [
            'users'          => User::whereCompanyPlanId(Auth::user()->company_id)->get(),
            'is_internal'    => $request->parent_ticket_id ? true : false,
            'parent_ticket'  => $parentTicket ?: false,
            'url'            => 'tickets/',
            'parent_tickets' => Ticket::scope()->where('status_id', '!=', 3)->whereNull('merged_parent_ticket_id')->OrderBy('public_id', 'DESC')->get(),
            'method'         => 'POST',
            'title'          => trans('texts.new_ticket'),
            'company'        => Auth::user()->company->load('clients.contacts', 'users'),
            'timezone'       => Auth::user()->company->timezone ? Auth::user()->company->timezone->name : DEFAULT_TIMEZONE,
            'datetimeFormat' => Auth::user()->company->getMomentDateTimeFormat(),
            'old'            => $request->old() ? $request->old() : $mockTicket,
            'clients'        => Client::scope()->with('contacts')->get(),
        ];

        return View::make('tickets.new_ticket', $data);
    }

    /**
     * @return Redirect
     */
    public function store(CreateTicketRequest $request)
    {
        $input = $request->input();
        $input['action'] = TICKET_AGENT_NEW;

        $ticket = $this->ticketService->save($input, $request->entity());

        return redirect("tickets/$ticket->public_id/edit");
    }

    /**
     * @param Request $request
     */
    public function inbound(TicketInboundRequest $request): void
    {
        $ticket = $request->entity();

        if (! $ticket) {
            Log::error('no ticket found - ? spam or new request?');
        } else {
            Log::error('ticket #' . $ticket->ticket_number . ' found');
        }
    }

    /**
     * @return View
     */
    public function merge($publicId)
    {
        $ticket = Ticket::scope($publicId)->first();

        $data = [
            'mergeableTickets' => $ticket->getClientMergeableTickets(),
            'ticket'           => $ticket,
            'company'          => Auth::user()->company,
            'title'            => trans('texts.ticket_merge'),
            'method'           => 'POST',
            'url'              => 'tickets/merge/',
            'entity'           => $ticket,
        ];

        return View::make('tickets.merge', $data);
    }

    /**
     * @return Redirect
     */
    public function actionMerge(TicketMergeRequest $request)
    {
        $ticket = $request->entity();
        $this->ticketService->mergeTicket($ticket, $request->input());

        Session::reflash();

        return redirect("tickets/$request->updated_ticket_id/edit");
    }

    /**
     * @return Collection
     */
    public function getTicketRelationCollection(\Illuminate\Http\Request $request)
    {
        return $this->ticketService->getRelationCollection($request);
    }

    /**
     * Add ticket relation entity.
     * returns a formatted URL.
     *
     * @return string
     */
    public function addEntity(TicketAddEntityRequest $request)
    {
        return $request->addEntity();
    }

    /**
     * Remove ticket.
     *
     * @return primary ID
     */
    public function removeEntity(TicketRemoveEntityRequest $request)
    {
        TicketRelation::destroy(request()->id);

        return request()->id;
    }

    /**
     * Algolia / Elasticsearch.
     *
     */
    public function search()
    {
        if (config('ninja.scout_driver') != null) {
            $result = TicketComment::search(request()->term)->where('agent_id', Auth::user()->id)->get()->pluck('description');

            return response()->json($result);
        }
    }
}
