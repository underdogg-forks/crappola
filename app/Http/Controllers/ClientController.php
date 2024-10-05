<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Jobs\Client\GenerateStatementData;
use App\Jobs\LoadPostmarkHistory;
use App\Jobs\ReactivatePostmarkEmail;
use App\Models\Account;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Task;
use App\Ninja\Datatables\ClientDatatable;
use App\Ninja\Repositories\ClientRepository;
use App\Services\ClientService;
use Auth;
use DropdownButton;
use Utils;
use View;

class ClientController extends BaseController
{
    protected \App\Services\ClientService $clientService;

    protected \App\Ninja\Repositories\ClientRepository $clientRepo;

    protected $entityType = ENTITY_CLIENT;

    public function __construct(ClientRepository $clientRepo, ClientService $clientService)
    {
        //parent::__construct();

        $this->clientRepo = $clientRepo;
        $this->clientService = $clientService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return \Illuminate\Support\Facades\View::make('list_wrapper', [
            'entityType' => ENTITY_CLIENT,
            'datatable'  => new ClientDatatable(),
            'title'      => trans('texts.clients'),
            'statuses'   => Client::getStatuses(),
        ]);
    }

    public function getDatatable()
    {
        $search = \Illuminate\Support\Facades\Request::input('sSearch');
        $userId = \Illuminate\Support\Facades\Auth::user()->filterIdByEntity(ENTITY_CLIENT);

        return $this->clientService->getDatatable($search, $userId);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(CreateClientRequest $request)
    {
        $client = $this->clientService->save($request->input());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.created_client'));

        return redirect()->to($client->getRoute());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show(ClientRequest $request)
    {
        $client = $request->entity();
        $user = \Illuminate\Support\Facades\Auth::user();
        $account = $user->account;

        //$user->can('view', [ENTITY_CLIENT, $client]);

        $actionLinks = [];
        if ($user->can('create', ENTITY_INVOICE)) {
            $actionLinks[] = ['label' => trans('texts.new_invoice'), 'url' => \Illuminate\Support\Facades\URL::to('/invoices/create/' . $client->public_id)];
        }
        if ($user->can('create', ENTITY_TASK)) {
            $actionLinks[] = ['label' => trans('texts.new_task'), 'url' => \Illuminate\Support\Facades\URL::to('/tasks/create/' . $client->public_id)];
        }
        if (Utils::hasFeature(FEATURE_QUOTES) && $user->can('create', ENTITY_QUOTE)) {
            $actionLinks[] = ['label' => trans('texts.new_quote'), 'url' => \Illuminate\Support\Facades\URL::to('/quotes/create/' . $client->public_id)];
        }
        if ($user->can('create', ENTITY_RECURRING_INVOICE)) {
            $actionLinks[] = ['label' => trans('texts.new_recurring_invoice'), 'url' => \Illuminate\Support\Facades\URL::to('/recurring_invoices/create/' . $client->public_id)];
        }

        if ( ! empty($actionLinks)) {
            $actionLinks[] = DropdownButton::DIVIDER;
        }

        if ($user->can('create', ENTITY_PAYMENT)) {
            $actionLinks[] = ['label' => trans('texts.enter_payment'), 'url' => \Illuminate\Support\Facades\URL::to('/payments/create/' . $client->public_id)];
        }

        if ($user->can('create', ENTITY_CREDIT)) {
            $actionLinks[] = ['label' => trans('texts.enter_credit'), 'url' => \Illuminate\Support\Facades\URL::to('/credits/create/' . $client->public_id)];
        }

        if ($user->can('create', ENTITY_EXPENSE)) {
            $actionLinks[] = ['label' => trans('texts.enter_expense'), 'url' => \Illuminate\Support\Facades\URL::to('/expenses/create/' . $client->public_id)];
        }

        $token = $client->getGatewayToken();

        $data = [
            'account'              => $account,
            'actionLinks'          => $actionLinks,
            'showBreadcrumbs'      => false,
            'client'               => $client,
            'credit'               => $client->getTotalCredit(),
            'title'                => trans('texts.view_client'),
            'hasRecurringInvoices' => $account->isModuleEnabled(ENTITY_RECURRING_INVOICE) && Invoice::scope()->recurring()->withArchived()->whereClientId($client->id)->count() > 0,
            'hasQuotes'            => $account->isModuleEnabled(ENTITY_QUOTE) && Invoice::scope()->quotes()->withArchived()->whereClientId($client->id)->count() > 0,
            'hasTasks'             => $account->isModuleEnabled(ENTITY_TASK) && Task::scope()->withArchived()->whereClientId($client->id)->count() > 0,
            'hasExpenses'          => $account->isModuleEnabled(ENTITY_EXPENSE) && Expense::scope()->withArchived()->whereClientId($client->id)->count() > 0,
            'gatewayLink'          => $token ? $token->gatewayLink() : false,
            'gatewayName'          => $token ? $token->gatewayName() : false,
        ];

        return \Illuminate\Support\Facades\View::make('clients.show', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(ClientRequest $request)
    {
        //Auth::user()->can('create', ENTITY_CLIENT);

        if (Client::scope()->withTrashed()->count() > \Illuminate\Support\Facades\Auth::user()->getMaxNumClients()) {
            return \Illuminate\Support\Facades\View::make('error', ['hideHeader' => true, 'error' => "Sorry, you've exceeded the limit of " . \Illuminate\Support\Facades\Auth::user()->getMaxNumClients() . ' clients']);
        }

        $data = [
            'client' => null,
            'method' => 'POST',
            'url'    => 'clients',
            'title'  => trans('texts.new_client'),
        ];

        $data = array_merge($data, self::getViewModel());

        return \Illuminate\Support\Facades\View::make('clients.edit', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit(ClientRequest $request)
    {
        $client = $request->entity();

        $data = [
            'client' => $client,
            'method' => 'PUT',
            'url'    => 'clients/' . $client->public_id,
            'title'  => trans('texts.edit_client'),
        ];

        $data = array_merge($data, self::getViewModel());

        if (\Illuminate\Support\Facades\Auth::user()->account->isNinjaAccount()) {
            if ($account = Account::whereId($client->public_id)->first()) {
                $data['planDetails'] = $account->getPlanDetails(false, false);
            }
        }

        return \Illuminate\Support\Facades\View::make('clients.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function update(UpdateClientRequest $request)
    {
        $client = $this->clientService->save($request->input(), $request->entity());

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.updated_client'));

        return redirect()->to($client->getRoute());
    }

    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('action');
        $ids = \Illuminate\Support\Facades\Request::input('public_id') ?: \Illuminate\Support\Facades\Request::input('ids');

        if ($action == 'purge' && ! auth()->user()->is_admin) {
            return redirect('dashboard')->withError(trans('texts.not_authorized'));
        }

        $count = $this->clientService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_client', $count);
        \Illuminate\Support\Facades\Session::flash('message', $message);

        if ($action == 'purge') {
            return redirect('dashboard')->withMessage($message);
        }

        return $this->returnBulk(ENTITY_CLIENT, $action, $ids);
    }

    public function statement($clientPublicId)
    {
        $statusId = request()->status_id;
        $startDate = request()->start_date;
        $endDate = request()->end_date;
        $account = \Illuminate\Support\Facades\Auth::user()->account;
        $client = Client::scope(request()->client_id)->with('contacts')->firstOrFail();

        if ( ! $startDate) {
            $startDate = Utils::today(false)->modify('-6 month')->format('Y-m-d');
            $endDate = Utils::today(false)->format('Y-m-d');
        }

        if (request()->json) {
            return dispatch_now(new GenerateStatementData($client, request()->all()));
        }

        $data = [
            'showBreadcrumbs' => false,
            'client'          => $client,
            'account'         => $account,
            'startDate'       => $startDate,
            'endDate'         => $endDate,
        ];

        return view('clients.statement', $data);
    }

    public function getEmailHistory()
    {
        $history = dispatch_now(new LoadPostmarkHistory(request()->email));

        return response()->json($history);
    }

    public function reactivateEmail()
    {
        $result = dispatch_now(new ReactivatePostmarkEmail(request()->bounce_id));

        return response()->json($result);
    }

    private static function getViewModel(): array
    {
        return [
            'data'         => \Illuminate\Support\Facades\Request::old('data'),
            'account'      => \Illuminate\Support\Facades\Auth::user()->account,
            'sizes'        => \Illuminate\Support\Facades\Cache::get('sizes'),
            'customLabel1' => \Illuminate\Support\Facades\Auth::user()->account->customLabel('client1'),
            'customLabel2' => \Illuminate\Support\Facades\Auth::user()->account->customLabel('client2'),
        ];
    }
}
