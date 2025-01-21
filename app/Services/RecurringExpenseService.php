<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Client;
use App\Models\Vendor;
use App\Ninja\Datatables\RecurringExpenseDatatable;
use App\Ninja\Repositories\RecurringExpenseRepository;
use Illuminate\Http\JsonResponse;

/**
 * Class RecurringExpenseService.
 */
class RecurringExpenseService extends BaseService
{
    /**
     * @var RecurringExpenseRepository
     */
    protected $recurringExpenseRepo;

    /**
     * @var DatatableService
     */
    protected $datatableService;

    /**
     * CreditService constructor.
     *
     * @param RecurringExpenseRepository $creditRepo
     */
    public function __construct(RecurringExpenseRepository $recurringExpenseRepo, DatatableService $datatableService)
    {
        $this->recurringExpenseRepo = $recurringExpenseRepo;
        $this->datatableService = $datatableService;
    }

    /**
     * @param mixed $recurringExpense
     *
     * @return mixed|null
     */
    public function save($data, $recurringExpense = false)
    {
        if (isset($data['client_id']) && $data['client_id']) {
            $data['client_id'] = Client::getPrivateId($data['client_id']);
        }

        if (isset($data['vendor_id']) && $data['vendor_id']) {
            $data['vendor_id'] = Vendor::getPrivateId($data['vendor_id']);
        }

        return $this->recurringExpenseRepo->save($data, $recurringExpense);
    }

    /**
     * @param       $clientPublicId
     * @param mixed $userId
     *
     */
    public function getDatatable($search, $userId)
    {
        $query = $this->recurringExpenseRepo->find($search);

        if (! Utils::hasPermission('view_expense')) {
            $query->where('recurring_expenses.user_id', '=', Auth::user()->id);
        }

        return $this->datatableService->createDatatable(new RecurringExpenseDatatable(), $query);
    }

    /**
     * @return CreditRepository
     */
    protected function getRepo()
    {
        return $this->recurringExpenseRepo;
    }
}
