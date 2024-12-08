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
 *
 * @property int                              $id
 * @property \Illuminate\Support\Carbon|null  $created_at
 * @property \Illuminate\Support\Carbon|null  $updated_at
 * @property \Illuminate\Support\Carbon|null  $deleted_at
 * @property int                              $account_id
 * @property int|null                         $vendor_id
 * @property int                              $user_id
 * @property int|null                         $client_id
 * @property int                              $is_deleted
 * @property string                           $amount
 * @property string                           $private_notes
 * @property string                           $public_notes
 * @property int|null                         $invoice_currency_id
 * @property int|null                         $expense_currency_id
 * @property int                              $should_be_invoiced
 * @property int|null                         $expense_category_id
 * @property string|null                      $tax_name1
 * @property string                           $tax_rate1
 * @property string|null                      $tax_name2
 * @property string                           $tax_rate2
 * @property int                              $frequency_id
 * @property string|null                      $start_date
 * @property string|null                      $end_date
 * @property string|null                      $last_sent_date
 * @property int                              $public_id
 * @property \App\Models\Account              $account
 * @property \App\Models\Client|null          $client
 * @property \App\Models\ExpenseCategory|null $expense_category
 * @property \App\Models\User                 $user
 * @property \App\Models\Vendor|null          $vendor
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense query()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereExpenseCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereExpenseCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereFrequencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereInvoiceCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereLastSentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereShouldBeInvoiced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereTaxName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereTaxName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereTaxRate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereTaxRate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RecurringExpense withoutTrashed()
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

    public function expense_category()
    {
        return $this->belongsTo(\App\Models\ExpenseCategory::class)->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class)->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class)->withTrashed();
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

    /**
     * @return string
     */
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
