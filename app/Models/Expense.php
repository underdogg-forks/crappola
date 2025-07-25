<?php

namespace App\Models;

use App\Events\ExpenseWasCreated;
use App\Events\ExpenseWasUpdated;
use App\Libraries\Utils;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Expense.
 */
class Expense extends EntityModel
{
    use PresentableTrait;
    // Expenses
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $presenter = 'App\Ninja\Presenters\ExpensePresenter';

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

    public static function getImportColumns()
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

    public static function getImportMap()
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

    public static function getStatuses($entityType = false)
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

    public static function calcStatusLabel($shouldBeInvoiced, $invoiceId, $balance, $paymentDate)
    {
        if ($invoiceId) {
            if ((float) $balance > 0) {
                $label = 'invoiced';
            } else {
                $label = 'billed';
            }
        } elseif ($shouldBeInvoiced) {
            $label = 'pending';
        } else {
            $label = 'logged';
        }

        $label = trans("texts.{$label}");

        if ($paymentDate) {
            $label = trans('texts.paid') . ' | ' . $label;
        }

        return $label;
    }

    public static function calcStatusClass($shouldBeInvoiced, $invoiceId, $balance)
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expense_category()
    {
        return $this->belongsTo('App\Models\ExpenseCategory')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client')->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function documents()
    {
        return $this->hasMany('App\Models\Document')->orderBy('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_type()
    {
        return $this->belongsTo('App\Models\PaymentType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recurring_expense()
    {
        return $this->belongsTo('App\Models\RecurringExpense')->withTrashed();
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

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/expenses/{$this->public_id}";
    }

    public function getEntityType()
    {
        return ENTITY_EXPENSE;
    }

    /**
     * @return bool
     */
    public function isExchanged()
    {
        return $this->invoice_currency_id != $this->expense_currency_id || $this->exchange_rate != 1;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->payment_date || $this->payment_type_id;
    }

    /**
     * @return float
     */
    public function convertedAmount()
    {
        return round($this->amount * $this->exchange_rate, 2);
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

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * @param      $query
     * @param null $bankdId
     *
     * @return mixed
     */
    public function scopeBankId($query, $bankdId = null)
    {
        if ($bankdId) {
            $query->whereBankId($bankdId);
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

Expense::creating(function ($expense) {
    $expense->setNullValues();
});

Expense::created(function ($expense) {
    event(new ExpenseWasCreated($expense));
});

Expense::updating(function ($expense) {
    $expense->setNullValues();
});

Expense::updated(function ($expense) {
    event(new ExpenseWasUpdated($expense));
});

Expense::deleting(function ($expense) {
    $expense->setNullValues();
});
