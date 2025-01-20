<?php

namespace App\Services;

use App\Ninja\Datatables\SubscriptionDatatable;
use App\Ninja\Repositories\SubscriptionRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class SubscriptionService.
 */
class SubscriptionService extends BaseService
{
    /**
     * @var SubscriptionRepository
     */
    protected $subscriptionRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * SubscriptionService constructor.
     *
     * @param SubscriptionRepository $subscriptionRepo
     * @param DatatableService       $datatableService
     */
    public function __construct(SubscriptionRepository $subscriptionRepo, DatatableService $datatableService)
    {
        $this->subscriptionRepo = $subscriptionRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param $userId
     *
     * @return JsonResponse
     */
    public function getDatatable($companyId)
    {
        $datatable = new SubscriptionDatatable(false);
        $query = $this->subscriptionRepo->find($companyId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return SubscriptionRepository
     */
    protected function getRepo()
    {
        return $this->subscriptionRepo;
    }
}
