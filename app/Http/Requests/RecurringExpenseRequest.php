<?php

namespace App\Http\Requests;

class RecurringExpenseRequest extends ExpenseRequest
{
    public $entityType = ENTITY_RECURRING_EXPENSE;
}
