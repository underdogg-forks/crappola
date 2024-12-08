<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCreditRequest;
use App\Http\Requests\CreditRequest;
use App\Http\Requests\UpdateCreditRequest;
use App\Models\Client;
use App\Models\Credit;
use App\Ninja\Datatables\CreditDatatable;
use App\Ninja\Repositories\CreditRepository;
use App\Services\CreditService;
use Utils;

class CreditController extends BaseController
{
    public $entityType = ENTITY_CREDIT;

    protected \App\Ninja\Repositories\CreditRepository $creditRepo;

    protected \App\Services\CreditService $creditService;

    public function __construct(CreditRepository $creditRepo, CreditService $creditService)
    {
        // parent::__construct();

        $this->creditRepo = $creditRepo;
        $this->creditService = $creditService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return \Illuminate\Support\Facades\View::make('list_wrapper', [
            'entityType' => ENTITY_CREDIT,
            'datatable'  => new CreditDatatable(),
            'title'      => trans('texts.credits'),
        ]);
    }

    public function getDatatable($clientPublicId = null)
    {
        return $this->creditService->getDatatable($clientPublicId, \Illuminate\Support\Facades\Request::input('sSearch'));
    }

    public function create(CreditRequest $request)
    {
        $data = [
            'clientPublicId' => \Illuminate\Support\Facades\Request::old('client') ?: ($request->client_id ?: 0),
            'credit'         => null,
            'method'         => 'POST',
            'url'            => 'credits',
            'title'          => trans('texts.new_credit'),
            'clients'        => Client::scope()->with('contacts')->orderBy('name')->get(),
        ];

        return \Illuminate\Support\Facades\View::make('credits.edit', $data);
    }

    public function edit(string $publicId)
    {
        $credit = Credit::withTrashed()->scope($publicId)->firstOrFail();

        $this->authorize('view', $credit);

        $credit->credit_date = Utils::fromSqlDate($credit->credit_date);

        $data = [
            'client'         => $credit->client,
            'clientPublicId' => $credit->client->public_id,
            'credit'         => $credit,
            'method'         => 'PUT',
            'url'            => 'credits/' . $publicId,
            'title'          => 'Edit Credit',
            'clients'        => null,
        ];

        return \Illuminate\Support\Facades\View::make('credits.edit', $data);
    }

    /**
     * @param $publicId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($publicId)
    {
        \Illuminate\Support\Facades\Session::reflash();

        return \Illuminate\Support\Facades\Redirect::to(sprintf('credits/%s/edit', $publicId));
    }

    public function update(UpdateCreditRequest $request)
    {
        $credit = $request->entity();

        return $this->save($credit);
    }

    public function store(CreateCreditRequest $request)
    {
        return $this->save();
    }

    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('action');
        $ids = \Illuminate\Support\Facades\Request::input('public_id') ?: \Illuminate\Support\Facades\Request::input('ids');
        $count = $this->creditService->bulk($ids, $action);

        if ($count > 0) {
            $message = Utils::pluralize($action . 'd_credit', $count);
            \Illuminate\Support\Facades\Session::flash('message', $message);
        }

        return $this->returnBulk(ENTITY_CREDIT, $action, $ids);
    }

    private function save($credit = null)
    {
        $credit = $this->creditService->save(\Illuminate\Support\Facades\Request::all(), $credit);

        $message = $credit->wasRecentlyCreated ? trans('texts.created_credit') : trans('texts.updated_credit');
        \Illuminate\Support\Facades\Session::flash('message', $message);

        return redirect()->to(sprintf('clients/%s#credits', $credit->client->public_id));
    }
}
