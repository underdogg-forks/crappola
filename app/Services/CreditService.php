<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Ninja\Datatables\CreditDatatable;
use App\Ninja\Repositories\CreditRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class CreditService.
 */
class CreditService extends BaseService
{
    protected CreditRepository $creditRepo;

    protected DatatableService $datatableService;

    /**
     * CreditService constructor.
     */
    public function __construct(CreditRepository $creditRepo, DatatableService $datatableService)
    {
        $this->creditRepo = $creditRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param            $data
     * @param null|mixed $credit
     *
     * @return mixed|null
     */
    public function save($data, $credit = null)
    {
        return $this->creditRepo->save($data, $credit);
    }

    /**
     * @param $clientPublicId
     * @param $search
     *
     * @return JsonResponse
     */
    public function getDatatable($clientPublicId, $search)
    {
        // we don't support bulk edit and hide the client on the individual client page
        $datatable = new CreditDatatable(true, $clientPublicId);
        $query = $this->creditRepo->find($clientPublicId, $search);

        if ( ! Utils::hasPermission('view_credit')) {
            $query->where('credits.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable($datatable, $query);
    }

    protected function getRepo(): CreditRepository
    {
        return $this->creditRepo;
    }
}
