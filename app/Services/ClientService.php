<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Client;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\NinjaRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class ClientService.
 */
class ClientService extends BaseService
{
    /**
     * @var ClientRepository
     */
    protected $clientRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * ClientService constructor.
     */
    public function __construct(ClientRepository $clientRepo, DatatableService $datatableService, NinjaRepository $ninjaRepo)
    {
        $this->clientRepo = $clientRepo;
        $this->ninjaRepo = $ninjaRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @return mixed|null
     */
    public function save($data, $client = null)
    {
        if (Auth::user()->company->isNinjaAccount() && isset($data['plan'])) {
            $this->ninjaRepo->updatePlanDetails($data['public_id'], $data);
        }

        return $this->clientRepo->save($data, $client);
    }

    public function getDatatable($search, $userId)
    {
        return datatables()
            ->eloquent(Client::query()
                ->with(['contacts' => function ($query): void {
                    $query->select('contacts.first_name', 'contacts.last_name', 'contacts.email');
                }])
                ->select('clients.id', 'clients.name', 'clients.id_number', 'clients.last_login', 'clients.balance')) // Adjust the query to your model
            ->addColumn('name', function ($model) {
                $str = link_to("clients/{$model->id}", $model->name ?: '')->toHtml();

                return $str;
            })
            ->addColumn('contact', function ($model) {
                return link_to("clients/{$model->id}", $model?->contact?->first_name . ' ' . $model?->contact?->last_name ?: '')->toHtml();
            })
            ->addColumn('email', function ($model) {
                return $model?->contact?->email;
            })
            ->addColumn('id_number', function ($model) {
                return $model->id_number;
                //Auth::user()->company()->clientNumbersEnabled(),
            })
            ->addColumn('last_login', function ($model) {
                return Utils::timestampToDateString(strtotime($model->last_login));
            })
            ->addColumn('balance', function ($model) {
                return Utils::formatMoney($model->balance, $model->currency_id, $model->country_id);
            })
            ->rawColumns(['name', 'contact'])
            ->make(true);
    }

    /**
     * @return ClientRepository
     */
    protected function getRepo()
    {
        return $this->clientRepo;
    }
}
