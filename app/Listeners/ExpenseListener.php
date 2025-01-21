<?php

namespace App\Listeners;

use App\Events\InvoiceWasDeleted;
use App\Models\Expense;
use App\Ninja\Repositories\ExpenseRepository;

/**
 * Class ExpenseListener.
 */
class ExpenseListener
{
    // Expenses
    protected ExpenseRepository $expenseRepo;

    /**
     * ExpenseListener constructor.
     */
    public function __construct(ExpenseRepository $expenseRepo)
    {
        $this->expenseRepo = $expenseRepo;
    }

    public function deletedInvoice(InvoiceWasDeleted $event): void
    {
        // Release any tasks associated with the deleted invoice
        Expense::where('invoice_id', '=', $event->invoice->id)
            ->update(['invoice_id' => null]);
    }
}
