<?php

namespace App\Models;

use App\Events\ExpenseWasCreated;
use App\Events\ExpenseWasUpdated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Utils;

/**
 * Class Expense.
 *
 * @property int                                                                 $id
 * @property \Illuminate\Support\Carbon|null                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                     $updated_at
 * @property \Illuminate\Support\Carbon|null                                     $deleted_at
 * @property int                                                                 $account_id
 * @property int|null                                                            $vendor_id
 * @property int                                                                 $user_id
 * @property int|null                                                            $invoice_id
 * @property int|null                                                            $client_id
 * @property int                                                                 $is_deleted
 * @property string                                                              $amount
 * @property string                                                              $exchange_rate
 * @property string|null                                                         $expense_date
 * @property string                                                              $private_notes
 * @property string                                                              $public_notes
 * @property int                                                                 $invoice_currency_id
 * @property int                                                                 $should_be_invoiced
 * @property int                                                                 $public_id
 * @property string|null                                                         $transaction_id
 * @property int|null                                                            $bank_id
 * @property int|null                                                            $expense_currency_id
 * @property int|null                                                            $expense_category_id
 * @property string|null                                                         $tax_name1
 * @property string                                                              $tax_rate1
 * @property string|null                                                         $tax_name2
 * @property string                                                              $tax_rate2
 * @property int|null                                                            $payment_type_id
 * @property string|null                                                         $payment_date
 * @property string|null                                                         $transaction_reference
 * @property int                                                                 $invoice_documents
 * @property int|null                                                            $recurring_expense_id
 * @property string|null                                                         $custom_value1
 * @property string|null                                                         $custom_value2
 * @property \App\Models\Account                                                 $account
 * @property \App\Models\Client|null                                             $client
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property int|null                                                            $documents_count
 * @property \App\Models\ExpenseCategory|null                                    $expense_category
 * @property \App\Models\Invoice|null                                            $invoice
 * @property \App\Models\PaymentType|null                                        $payment_type
 * @property \App\Models\RecurringExpense|null                                   $recurring_expense
 * @property \App\Models\User                                                    $user
 * @property \App\Models\Vendor|null                                             $vendor
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Expense bankId($bankId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense query()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExchangeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExpenseCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExpenseCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereExpenseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereInvoiceCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereInvoiceDocuments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePaymentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePublicNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereRecurringExpenseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereShouldBeInvoiced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTaxName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTaxName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTaxRate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTaxRate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Expense extends EntityModel
{
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
        'expense_date',
        'invoice_currency_id',
        'amount',
        'foreign_amount',
        'exchange_rate',
        'private_notes',
        'public_notes',
        'bank_id',
        'transaction_id',
        'expense_category_id',
        'tax_rate1',
        'tax_name1',
        'tax_rate2',
        'tax_name2',
        'payment_date',
        'payment_type_id',
        'transaction_reference',
        'invoice_documents',
        'should_be_invoiced',
        'custom_value1',
        'custom_value2',
    ];

    protected $casts = ['deleted_at' => 'datetime'];

    public static function getImportColumns(): array
    {
        return [
            'client',
            'vendor',
            'amount',
            'public_notes',
            'private_notes',
            'expense_category',
            'expense_date',
            'payment_type',
            'payment_date',
            'transaction_reference',
        ];
    }

    public static function getImportMap(): array
    {
        return [
            'amount|total'          => 'amount',
            'category'              => 'expense_category',
            'client'                => 'client',
            'vendor'                => 'vendor',
            'notes|details^private' => 'public_notes',
            'notes|details^public'  => 'private_notes',
            'date^payment'          => 'expense_date',
            'payment type'          => 'payment_type',
            'payment date'          => 'payment_date',
            'reference'             => 'transaction_reference',
        ];
    }

    public static function getStatuses($entityType = false): array
    {
        return [EXPENSE_STATUS_LOGGED => trans('texts.logged'), EXPENSE_STATUS_PENDING => trans('texts.pending'), EXPENSE_STATUS_INVOICED => trans('texts.invoiced'), EXPENSE_STATUS_BILLED => trans('texts.billed'), EXPENSE_STATUS_PAID => trans('texts.paid'), EXPENSE_STATUS_UNPAID => trans('texts.unpaid')];
    }

    public static function calcStatusLabel($shouldBeInvoiced, $invoiceId, $balance, $paymentDate)
    {
        if ($invoiceId) {
            $label = (float) $balance > 0 ? 'invoiced' : 'billed';
        } elseif ($shouldBeInvoiced) {
            $label = 'pending';
        } else {
            $label = 'logged';
        }

        $label = trans('texts.' . $label);

        if ($paymentDate) {
            return trans('texts.paid') . ' | ' . $label;
        }

        return $label;
    }

    public static function calcStatusClass($shouldBeInvoiced, $invoiceId, $balance): string
    {
        if ($invoiceId) {
            if ((float) $balance > 0) {
                return 'default';
            }

            return 'success';
        }

        if ($shouldBeInvoiced) {
            return 'warning';
        }

        return 'primary';
    }

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

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class)->withTrashed();
    }

    public function documents()
    {
        return $this->hasMany(\App\Models\Document::class)->orderBy('id');
    }

    public function payment_type()
    {
        return $this->belongsTo(\App\Models\PaymentType::class);
    }

    public function recurring_expense()
    {
        return $this->belongsTo(\App\Models\RecurringExpense::class)->withTrashed();
    }

    public function getName()
    {
        if ($this->transaction_id) {
            return $this->transaction_id;
        }

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
        return '/expenses/' . $this->public_id;
    }

    public function getEntityType(): string
    {
        return ENTITY_EXPENSE;
    }

    public function isExchanged(): bool
    {
        return $this->invoice_currency_id != $this->expense_currency_id || $this->exchange_rate != 1;
    }

    public function isPaid(): bool
    {
        return $this->payment_date || $this->payment_type_id;
    }

    public function convertedAmount(): float
    {
        return round($this->amount * $this->exchange_rate, 2);
    }

    public function toArray()
    {
        $array = parent::toArray();

        if (empty($this->visible) || in_array('converted_amount', $this->visible)) {
            $array['converted_amount'] = $this->convertedAmount();
        }

        return $array;
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * @param null $bankId
     *
     * @return mixed
     */
    public function scopeBankId($query, $bankId = null)
    {
        if ($bankId) {
            $query->whereBankId($bankId);
        }

        return $query;
    }

    public function amountWithTax(): float|int|array
    {
        return $this->amount + $this->taxAmount();
    }

    public function taxAmount()
    {
        return Utils::calculateTaxes($this->amount, $this->tax_rate1, $this->tax_rate2);
    }

    public function statusClass(): string
    {
        $balance = $this->invoice ? $this->invoice->balance : 0;

        return static::calcStatusClass($this->should_be_invoiced, $this->invoice_id, $balance);
    }

    public function statusLabel(): string
    {
        $balance = $this->invoice ? $this->invoice->balance : 0;

        return static::calcStatusLabel($this->should_be_invoiced, $this->invoice_id, $balance, $this->payment_date);
    }
}

Expense::creating(function ($expense): void {
    $expense->setNullValues();
});

Expense::created(function ($expense): void {
    event(new ExpenseWasCreated($expense));
});

Expense::updating(function ($expense): void {
    $expense->setNullValues();
});

Expense::updated(function ($expense): void {
    event(new ExpenseWasUpdated($expense));
});

Expense::deleting(function ($expense): void {
    $expense->setNullValues();
});
