<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketTemplateRequest;
use App\Libraries\Utils;
use App\Models\TicketTemplate;
use App\Services\TicketTemplateService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class TicketTemplateController extends BaseController
{
    protected TicketTemplateService $ticketTemplateService;

    /**
     * TicketTemplateController constructor.
     */
    public function __construct(TicketTemplateService $ticketTemplateService)
    {
        $this->ticketTemplateService = $ticketTemplateService;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return Redirect::to('settings/' . ACCOUNT_TICKETS . '#templates');
    }

    public function getDatatable($clientPublicId = null)
    {
        return $this->ticketTemplateService->getDatatable();
    }

    /**
     * @return mixed
     */
    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("ticket_templates/$publicId/edit");
    }

    /**
     * @return mixed
     */
    public function edit($publicId)
    {
        $ticketTemplate = TicketTemplate::scope($publicId)->firstOrFail();

        $data = self::getViewModel($ticketTemplate);

        $data = array_merge($data, [
            'method' => 'PUT',
            'url'    => '/ticket_templates/' . $publicId,
        ]);

        return View::make('companies.ticket_templates', $data);
    }

    /**
     * @return array{company: mixed, user: Authenticatable|null, config: false, ticket_templates: mixed}
     */
    private function getViewModel($ticketTemplate): array
    {
        $user = Auth::user();

        $company = $user->company;

        return [
            'company'          => $company,
            'user'             => $user,
            'config'           => false,
            'ticket_templates' => $ticketTemplate,
        ];
    }

    /**
     * @return mixed
     */
    public function update($publicId)
    {
        return $this->save($publicId);
    }

    /**
     * @param bool $ticketTemplatePublicId
     *
     * @return mixed
     */
    public function save($ticketTemplatePublicId = false)
    {
        if ($ticketTemplatePublicId) {
            $ticketTemplate = TicketTemplate::scope($ticketTemplatePublicId)->firstOrFail();
        } else {
            $ticketTemplate = TicketTemplate::createNew();
        }

        $ticketTemplate->name = $request->get('name');
        $ticketTemplate->description = $request->get('description');
        $ticketTemplate->save();

        $message = $ticketTemplatePublicId ? trans('texts.updated_ticket_template') : trans('texts.created_ticket_template');

        Session::flash('message', $message);

        return Redirect::to('settings/' . ACCOUNT_TICKETS . '#templates');
    }

    /**
     * @return mixed
     */
    public function store(CreateTicketTemplateRequest $request)
    {
        return $this->save();
    }

    /**
     * Displays the form for company creation.
     */
    public function create()
    {
        $data = self::getViewModel(null);

        $data = array_merge($data, [
            'method' => 'POST',
            'url'    => '/ticket_templates/create',
            'title'  => trans('texts.add_template'),
        ]);

        return View::make('companies.ticket_templates', $data);
    }

    /**
     * @return mixed
     */
    public function bulk()
    {
        $action = $request->get('bulk_action');

        $ids = $request->get('bulk_public_id');

        $count = $this->ticketTemplateService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_ticket_template', $count);

        Session::flash('message', $message);

        return Redirect::to('settings/' . ACCOUNT_TICKETS . '#templates');
    }
}
