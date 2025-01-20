<?php

namespace App\Models;

use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Events\PaymentWasCreated;
use App\Events\PaymentWasRefunded;
use App\Events\PaymentWasVoided;
use App\Ninja\Presenters\PaymentPresenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Event;
use Laracasts\Presenter\PresentableTrait;
use stdClass;

/**
 * Class Payment.
 */
class Payment extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    public static $statusClasses = [
        PAYMENT_STATUS_PENDING            => 'info',
        PAYMENT_STATUS_COMPLETED          => 'success',
        PAYMENT_STATUS_FAILED             => 'danger',
        PAYMENT_STATUS_PARTIALLY_REFUNDED => 'primary',
        PAYMENT_STATUS_VOIDED             => 'default',
        PAYMENT_STATUS_REFUNDED           => 'default',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'transaction_reference',
        'private_notes',
        'exchange_rate',
        'exchange_currency_id',
    ];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $presenter = PaymentPresenter::class;

    /**
     * @return mixed
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
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
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @return BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class)->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function account_gateway()
    {
        return $this->belongsTo(AccountGateway::class)->withTrashed();
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
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * @return BelongsTo
     */
    public function payment_status()
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return "/payments/{$this->public_id}/edit";
    }

    /*
    public function getAmount()
    {
        return Utils::formatMoney($this->amount, $this->client->getCurrencyId());
    }
    */

    public function scopeExcludeFailed($query)
    {
        $query->whereNotIn('payment_status_id', [PAYMENT_STATUS_VOIDED, PAYMENT_STATUS_FAILED]);

        return $query;
    }

    /**
     * @return mixed
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return trim("payment {$this->transaction_reference}");
    }

    public function isPending(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_PENDING;
    }

    public function isFailedOrVoided(): bool
    {
        if ($this->isFailed()) {
            return true;
        }

        return $this->isVoided();
    }

    public function isFailed(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_FAILED;
    }

    public function isVoided(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_VOIDED;
    }

    public function recordRefund($amount = null): bool
    {
        if ($this->isRefunded()) {
            return false;
        }
        if ($this->isVoided()) {
            return false;
        }
        if (! $amount) {
            $amount = $this->amount;
        }

        $new_refund = min($this->amount, $this->refunded + $amount);
        $refund_change = $new_refund - $this->refunded;

        if ($refund_change) {
            $this->refunded = $new_refund;
            $this->payment_status_id = $this->refunded == $this->amount ? PAYMENT_STATUS_REFUNDED : PAYMENT_STATUS_PARTIALLY_REFUNDED;
            $this->save();

            Event::dispatch(new PaymentWasRefunded($this, $refund_change));
        }

        return true;
    }

    public function isRefunded(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_REFUNDED;
    }

    public function markVoided(): bool
    {
        if ($this->isVoided()) {
            return false;
        }
        if ($this->isPartiallyRefunded()) {
            return false;
        }
        if ($this->isRefunded()) {
            return false;
        }
        Event::dispatch(new PaymentWasVoided($this));

        $this->refunded = $this->amount;
        $this->payment_status_id = PAYMENT_STATUS_VOIDED;
        $this->save();

        return true;
    }

    public function isPartiallyRefunded(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_PARTIALLY_REFUNDED;
    }

    public function markComplete(): void
    {
        $this->payment_status_id = PAYMENT_STATUS_COMPLETED;
        $this->save();
        Event::dispatch(new PaymentCompleted($this));
    }

    /**
     * @param string $failureMessage
     */
    public function markFailed($failureMessage = ''): void
    {
        $this->payment_status_id = PAYMENT_STATUS_FAILED;
        $this->gateway_error = $failureMessage;
        $this->save();
        Event::dispatch(new PaymentFailed($this));
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return ENTITY_PAYMENT;
    }

    public function canBeRefunded(): bool
    {
        if ($this->getCompletedAmount() <= 0) {
            return false;
        }
        if ($this->isCompleted()) {
            return true;
        }

        return $this->isPartiallyRefunded();
    }

    /**
     * @return mixed
     */
    public function getCompletedAmount()
    {
        return $this->amount - $this->refunded;
    }

    public function isCompleted(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_COMPLETED;
    }

    public function isExchanged(): bool
    {
        return $this->exchange_currency_id || $this->exchange_rate != 1;
    }

    /**
     * @return mixed|null|stdClass|string
     */
    public function getBankDataAttribute()
    {
        if (! $this->routing_number) {
            return;
        }

        return PaymentMethod::lookupBankData($this->routing_number);
    }

    public function getBankNameAttribute($bank_name)
    {
        if ($bank_name) {
            return $bank_name;
        }
        $bankData = $this->bank_data;

        return $bankData ? $bankData->name : null;
    }

    /**
     * @return null|string
     */
    public function getLast4Attribute($value)
    {
        return $value ? str_pad($value, 4, '0', STR_PAD_LEFT) : null;
    }

    public function statusClass()
    {
        return static::calcStatusClass($this->payment_status_id);
    }

    public static function calcStatusClass($statusId)
    {
        return static::$statusClasses[$statusId];
    }

    public function statusLabel()
    {
        $amount = $this->company->formatMoney($this->refunded, $this->client);

        return static::calcStatusLabel($this->payment_status_id, $this->payment_status->name, $amount);
    }

    public static function calcStatusLabel($statusId, $statusName, $amount)
    {
        if ($statusId == PAYMENT_STATUS_PARTIALLY_REFUNDED) {
            return trans('texts.status_partially_refunded_amount', [
                'amount' => $amount,
            ]);
        }

        return trans('texts.status_' . strtolower($statusName));
    }

    public function invoiceJsonBackup()
    {
        $activity = Activity::wherePaymentId($this->id)
            ->whereActivityTypeId(ACTIVITY_TYPE_CREATE_PAYMENT)
            ->get(['json_backup'])
            ->first();

        return $activity->json_backup;
    }
}

Payment::creating(function ($payment): void {
});

Payment::created(function ($payment): void {
    event(new PaymentWasCreated($payment));
});
