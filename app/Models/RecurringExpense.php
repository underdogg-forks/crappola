<?php

namespace App\Models;

//use App\Events\ExpenseWasCreated;
//use App\Events\ExpenseWasUpdated;
use App\Libraries\Utils;
use App\Models\Traits\HasRecurrence;
use App\Ninja\Presenters\ExpensePresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Expense.
 *
 * @property int                  $id
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * @property Carbon|null          $deleted_at
 * @property int                  $account_id
 * @property int|null             $vendor_id
 * @property int                  $user_id
 * @property int|null             $client_id
 * @property int                  $is_deleted
 * @property string               $amount
 * @property string               $private_notes
 * @property string               $public_notes
 * @property int|null             $invoice_currency_id
 * @property int|null             $expense_currency_id
 * @property int                  $should_be_invoiced
 * @property int|null             $expense_category_id
 * @property string|null          $tax_name1
 * @property string               $tax_rate1
 * @property string|null          $tax_name2
 * @property string               $tax_rate2
 * @property int                  $frequency_id
 * @property string|null          $start_date
 * @property string|null          $end_date
 * @property string|null          $last_sent_date
 * @property int                  $public_id
 * @property Account              $account
 * @property Client|null          $client
 * @property ExpenseCategory|null $expense_category
 * @property User                 $user
 * @property Vendor|null          $vendor
 *
 * @method static Builder|RecurringExpense newModelQuery()
 * @method static Builder|RecurringExpense newQuery()
 * @method static Builder|RecurringExpense onlyTrashed()
 * @method static Builder|RecurringExpense query()
 * @method static Builder|RecurringExpense scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|RecurringExpense whereAccountId($value)
 * @method static Builder|RecurringExpense whereAmount($value)
 * @method static Builder|RecurringExpense whereClientId($value)
 * @method static Builder|RecurringExpense whereCreatedAt($value)
 * @method static Builder|RecurringExpense whereDeletedAt($value)
 * @method static Builder|RecurringExpense whereEndDate($value)
 * @method static Builder|RecurringExpense whereExpenseCategoryId($value)
 * @method static Builder|RecurringExpense whereExpenseCurrencyId($value)
 * @method static Builder|RecurringExpense whereFrequencyId($value)
 * @method static Builder|RecurringExpense whereId($value)
 * @method static Builder|RecurringExpense whereInvoiceCurrencyId($value)
 * @method static Builder|RecurringExpense whereIsDeleted($value)
 * @method static Builder|RecurringExpense whereLastSentDate($value)
 * @method static Builder|RecurringExpense wherePrivateNotes($value)
 * @method static Builder|RecurringExpense wherePublicId($value)
 * @method static Builder|RecurringExpense wherePublicNotes($value)
 * @method static Builder|RecurringExpense whereShouldBeInvoiced($value)
 * @method static Builder|RecurringExpense whereStartDate($value)
 * @method static Builder|RecurringExpense whereTaxName1($value)
 * @method static Builder|RecurringExpense whereTaxName2($value)
 * @method static Builder|RecurringExpense whereTaxRate1($value)
 * @method static Builder|RecurringExpense whereTaxRate2($value)
 * @method static Builder|RecurringExpense whereUpdatedAt($value)
 * @method static Builder|RecurringExpense whereUserId($value)
 * @method static Builder|RecurringExpense whereVendorId($value)
 * @method static Builder|RecurringExpense withActiveOrSelected($id = false)
 * @method static Builder|RecurringExpense withArchived()
 * @method static Builder|RecurringExpense withTrashed()
 * @method static Builder|RecurringExpense withoutTrashed()
 *
 * @mixin \Eloquent
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
    protected $presenter = ExpensePresenter::class;

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

    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class)->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function getName()
    {
        if ($this->public_notes) {
            return Utils::truncateString($this->public_notes, 16);
        }

        return '#' . $this->public_id;
    }

    public function getDisplayName()
    {
        return $this->getName();
    }

    public function getRoute(): string
    {
        return sprintf('/recurring_expenses/%s/edit', $this->public_id);
    }

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
