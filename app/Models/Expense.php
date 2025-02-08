<?php

namespace App\Models;

use App\Events\ExpenseWasCreated;
use App\Events\ExpenseWasUpdated;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Expense.
 */
class Expense extends EntityModel
{
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
        'expense_date',
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
        'transaction_reference',
        'invoice_documents',
        'should_be_invoiced',
        'custom_value1',
        'custom_value2',
    ];

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

    /**
     * @return mixed[]
     */
    public static function getStatuses($entityType = false): array
    {
        $statuses = [];
        $statuses[EXPENSE_STATUS_LOGGED] = trans('texts.logged');
        $statuses[EXPENSE_STATUS_PENDING] = trans('texts.pending');
        $statuses[EXPENSE_STATUS_INVOICED] = trans('texts.invoiced');
        $statuses[EXPENSE_STATUS_BILLED] = trans('texts.billed');
        $statuses[EXPENSE_STATUS_PAID] = trans('texts.paid');
        $statuses[EXPENSE_STATUS_UNPAID] = trans('texts.unpaid');

        return $statuses;
    }

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
    public function invoice()
    {
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    /**
     * @return mixed
     */
    public function documents(): Builder
    {
        return $this->hasMany(Document::class)->orderBy('id');
    }

    /**
     * @return BelongsTo
     */
    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }

    /**
     * @return BelongsTo
     */
    public function recurring_expense()
    {
        return $this->belongsTo(RecurringExpense::class);
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
        if ($this->transaction_id) {
            return $this->transaction_id;
        }
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
        return "/expenses/{$this->public_id}";
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_EXPENSE;
    }

    public function isExchanged(): bool
    {
        return $this->invoice_currency_id != $this->invoice_currency_id || $this->exchange_rate != 1;
    }

    public function isPaid(): bool
    {
        return $this->payment_date || $this->payment_type_id;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        if (empty($this->visible) || in_array('converted_amount', $this->visible)) {
            $array['converted_amount'] = $this->convertedAmount();
        }

        return $array;
    }

    public function convertedAmount(): float
    {
        return round($this->amount * $this->exchange_rate, 2);
    }

    /**
     * @return mixed
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * @return mixed
     */
    public function scopeBankId($query, $bankdId = null)
    {
        if ($bankdId) {
            $query->whereBankId($bankId);
        }

        return $query;
    }

    public function amountWithTax()
    {
        return $this->amount + $this->taxAmount();
    }

    public function taxAmount()
    {
        return Utils::calculateTaxes($this->amount, $this->tax_rate1, $this->tax_rate2);
    }

    public function statusClass()
    {
        $balance = $this->invoice ? $this->invoice->balance : 0;

        return static::calcStatusClass($this->should_be_invoiced, $this->invoice_id, $balance);
    }

    public static function calcStatusClass($shouldBeInvoiced, $invoiceId, $balance): string
    {
        if ($invoiceId) {
            if (floatval($balance) > 0) {
                return 'default';
            }

            return 'success';
        }
        if ($shouldBeInvoiced) {
            return 'warning';
        }

        return 'primary';
    }

    public function statusLabel()
    {
        $balance = $this->invoice ? $this->invoice->balance : 0;

        return static::calcStatusLabel($this->should_be_invoiced, $this->invoice_id, $balance, $this->payment_date);
    }

    public static function calcStatusLabel($shouldBeInvoiced, $invoiceId, $balance, $paymentDate)
    {
        if ($invoiceId) {
            $label = floatval($balance) > 0 ? 'invoiced' : 'billed';
        } elseif ($shouldBeInvoiced) {
            $label = 'pending';
        } else {
            $label = 'logged';
        }

        $label = trans("texts.{$label}");

        if ($paymentDate) {
            return trans('texts.paid') . ' | ' . $label;
        }

        return $label;
    }

    public static function calcStatusClass($shouldBeInvoiced, $invoiceId, $balance)
    {
        if ($invoiceId) {
            if (floatval($balance) > 0) {
                return 'default';
            } else {
                return 'success';
            }
        } elseif ($shouldBeInvoiced) {
            return 'warning';
        } else {
            return 'primary';
        }
    }

    public function statusClass()
    {
        $balance = $this->invoice ? $this->invoice->balance : 0;

        return static::calcStatusClass($this->should_be_invoiced, $this->invoice_id, $balance);
    }

    public function statusLabel()
    {
        $balance = $this->invoice ? $this->invoice->balance : 0;

        return static::calcStatusLabel($this->should_be_invoiced, $this->invoice_id, $balance, $this->payment_date);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
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
