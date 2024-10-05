<?php

namespace App\Models;

//use App\Events\ExpenseWasCreated;
//use App\Events\ExpenseWasUpdated;
use App\Models\Traits\HasRecurrence;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Utils;

/**
 * Class Expense.
 */
class RecurringExpense extends EntityModel
{
    use HasRecurrence;
    use PresentableTrait;
    // Expenses
    use SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\ExpensePresenter::class;

    /**
     * @var array
     */
    protected $fillable = [
        'client_id',
        'vendor_id',
        'expense_currency_id',
        //'invoice_currency_id',
        //'exchange_rate',
        'amount',
        'private_notes',
        'public_notes',
        'expense_category_id',
        'tax_rate1',
        'tax_name1',
        'tax_rate2',
        'tax_name2',
        'should_be_invoiced',
        //'start_date',
        //'end_date',
        'frequency_id',
    ];
    protected $casts = ['deleted_at' => 'datetime'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expense_category()
    {
        return $this->belongsTo(\App\Models\ExpenseCategory::class)->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if ($this->public_notes) {
            return Utils::truncateString($this->public_notes, 16);
        }

        return '#' . $this->public_id;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return sprintf('/recurring_expenses/%s/edit', $this->public_id);
    }

    /**
     * @return mixed
     */
    public function getEntityType(): string
    {
        return ENTITY_RECURRING_EXPENSE;
    }

    public function amountWithTax(): float|int|array
    {
        return $this->amount + Utils::calculateTaxes($this->amount, $this->tax_rate1, $this->tax_rate2);
    }
}

RecurringExpense::creating(function ($expense): void {
    $expense->setNullValues();
});

RecurringExpense::created(function ($expense): void {
    //event(new ExpenseWasCreated($expense));
});

RecurringExpense::updating(function ($expense): void {
    $expense->setNullValues();
});

RecurringExpense::updated(function ($expense): void {
    //event(new ExpenseWasUpdated($expense));
});

RecurringExpense::deleting(function ($expense): void {
    $expense->setNullValues();
});
