<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCreditRequest;
use App\Http\Requests\CreditRequest;
use App\Http\Requests\UpdateCreditRequest;
use App\Libraries\Utils;
use App\Models\Client;
use App\Models\Credit;
use App\Ninja\Datatables\CreditDatatable;
use App\Ninja\Repositories\CreditRepository;
use App\Services\CreditService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class CreditController extends BaseController
{
    protected $creditRepo;

    protected $creditService;

    protected $entityType = ENTITY_CREDIT;

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
        return View::make('list_wrapper', [
            'entityType' => ENTITY_CREDIT,
            'datatable'  => new CreditDatatable(),
            'title'      => trans('texts.credits'),
        ]);
    }

    public function getDatatable($clientPublicId = null)
    {
        return $this->creditService->getDatatable($clientPublicId, Request::input('sSearch'));
    }

    public function create(CreditRequest $request)
    {
        $data = [
            'clientPublicId' => Request::old('client') ? Request::old('client') : ($request->client_id ?: 0),
            'credit'         => null,
            'method'         => 'POST',
            'url'            => 'credits',
            'title'          => trans('texts.new_credit'),
            'clients'        => Client::scope()->with('contacts')->orderBy('name')->get(),
        ];

        return View::make('credits.edit', $data);
    }

    public function edit($publicId)
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

        return View::make('credits.edit', $data);
    }

    /**
     * @param $publicId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("credits/{$publicId}/edit");
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
        $action = Request::input('action');
        $ids = Request::input('public_id') ? Request::input('public_id') : Request::input('ids');
        $count = $this->creditService->bulk($ids, $action);

        if ($count > 0) {
            $message = Utils::pluralize($action . 'd_credit', $count);
            Session::flash('message', $message);
        }

        return $this->returnBulk(ENTITY_CREDIT, $action, $ids);
    }

    private function save($credit = null)
    {
        $credit = $this->creditService->save(Request::all(), $credit);

        $message = $credit->wasRecentlyCreated ? trans('texts.created_credit') : trans('texts.updated_credit');
        Session::flash('message', $message);

        return redirect()->to("clients/{$credit->client->public_id}#credits");
    }
}
