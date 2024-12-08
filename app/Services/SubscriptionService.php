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
    protected SubscriptionRepository $subscriptionRepo;

    protected DatatableService $datatableService;

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
    public function getDatatable($accountId)
    {
        $datatable = new SubscriptionDatatable(false);
        $query = $this->subscriptionRepo->find($accountId);

        return $this->datatableService->createDatatable($datatable, $query);
    }

    /**
     * @return SubscriptionRepository
     */
    protected function getRepo(): SubscriptionRepository
    {
        return $this->subscriptionRepo;
    }
}
