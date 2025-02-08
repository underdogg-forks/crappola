<?php

namespace App\Models;

//use App\Events\ExpenseWasCreated;
//use App\Events\ExpenseWasUpdated;
use App\Libraries\Utils;
use App\Models\Traits\HasRecurrence;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

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
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $presenter = ExpensePresenter::class;

    /**
     * @var array
     */
    protected $fillable = [
        'client_id',
        'vendor_id',
        'invoice_currency_id',
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
        'custom_value1',
        'custom_value2',
    ];

    /**
     * @return BelongsTo
     */
    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->getName();
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
     * @return string
     */
    public function getRoute()
    {
        return "/recurring_expenses/{$this->public_id}/edit";
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_RECURRING_EXPENSE;
    }

    public function amountWithTax()
    {
        return $this->amount + Utils::calculateTaxes($this->amount, $this->tax_rate1, $this->tax_rate2);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
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
