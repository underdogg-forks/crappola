<?php

namespace App\Models;

use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Events\PaymentWasCreated;
use App\Events\PaymentWasRefunded;
use App\Events\PaymentWasVoided;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use stdClass;

/**
 * Class Payment.
 *
 * @property int                             $id
 * @property int                             $invoice_id
 * @property int                             $account_id
 * @property int                             $client_id
 * @property int|null                        $contact_id
 * @property int|null                        $invitation_id
 * @property int|null                        $user_id
 * @property int|null                        $account_gateway_id
 * @property int|null                        $payment_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int                             $is_deleted
 * @property string                          $amount
 * @property string|null                     $payment_date
 * @property string|null                     $transaction_reference
 * @property string|null                     $payer_id
 * @property int                             $public_id
 * @property string                          $refunded
 * @property int                             $payment_status_id
 * @property int|null                        $routing_number
 * @property null|string                     $last4
 * @property string|null                     $expiration
 * @property string|null                     $gateway_error
 * @property string|null                     $email
 * @property int|null                        $payment_method_id
 * @property null                            $bank_name
 * @property string|null                     $ip
 * @property string|null                     $credit_ids
 * @property string|null                     $private_notes
 * @property string                          $exchange_rate
 * @property int                             $exchange_currency_id
 * @property \App\Models\Account             $account
 * @property \App\Models\AccountGateway|null $account_gateway
 * @property \App\Models\Client              $client
 * @property \App\Models\Contact|null        $contact
 * @property mixed|null|stdClass|string      $bank_data
 * @property \App\Models\Invitation|null     $invitation
 * @property \App\Models\Invoice             $invoice
 * @property \App\Models\PaymentMethod|null  $payment_method
 * @property \App\Models\PaymentStatus       $payment_status
 * @property \App\Models\PaymentType|null    $payment_type
 * @property \App\Models\User|null           $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Payment dateRange($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment excludeFailed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment scope(bool $publicId = false, bool $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAccountGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreditIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereExchangeCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereExchangeRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereExpiration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGatewayError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInvitationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereLast4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePrivateNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereRefunded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereRoutingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment withArchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment withoutTrashed()
 *
 * @mixin \Eloquent
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
     * @var string
     */
    protected $presenter = \App\Ninja\Presenters\PaymentPresenter::class;

    protected $casts = ['deleted_at' => 'datetime'];

    public static function calcStatusLabel($statusId, $statusName, $amount)
    {
        if ($statusId == PAYMENT_STATUS_PARTIALLY_REFUNDED) {
            return trans('texts.status_partially_refunded_amount', [
                'amount' => $amount,
            ]);
        }

        return trans('texts.status_' . mb_strtolower($statusName));
    }

    public static function calcStatusClass($statusId)
    {
        return static::$statusClasses[$statusId];
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class)->withTrashed();
    }

    public function invitation()
    {
        return $this->belongsTo(\App\Models\Invitation::class);
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class)->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function contact()
    {
        return $this->belongsTo(\App\Models\Contact::class)->withTrashed();
    }

    public function account_gateway()
    {
        return $this->belongsTo(\App\Models\AccountGateway::class)->withTrashed();
    }

    public function payment_type()
    {
        return $this->belongsTo(\App\Models\PaymentType::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(\App\Models\PaymentMethod::class);
    }

    public function payment_status()
    {
        return $this->belongsTo(\App\Models\PaymentStatus::class);
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return sprintf('/payments/%s/edit', $this->public_id);
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

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function getName(): string
    {
        return trim('payment ' . $this->transaction_reference);
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_FAILED;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isPartiallyRefunded(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_PARTIALLY_REFUNDED;
    }

    /**
     * @return bool
     */
    public function isRefunded(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_REFUNDED;
    }

    /**
     * @return bool
     */
    public function isVoided(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_VOIDED;
    }

    public function isFailedOrVoided()
    {
        if ($this->isFailed()) {
            return true;
        }

        return $this->isVoided();
    }

    /**
     * @param null $amount
     *
     * @return bool
     */
    public function recordRefund($amount = null): bool
    {
        if ($this->isRefunded() || $this->isVoided()) {
            return false;
        }

        if ( ! $amount) {
            $amount = $this->amount;
        }

        $new_refund = min($this->amount, $this->refunded + $amount);
        $refund_change = $new_refund - $this->refunded;

        if ($refund_change) {
            $this->refunded = $new_refund;
            $this->payment_status_id = $this->refunded == $this->amount ? PAYMENT_STATUS_REFUNDED : PAYMENT_STATUS_PARTIALLY_REFUNDED;
            $this->save();

            \Illuminate\Support\Facades\Event::dispatch(new PaymentWasRefunded($this, $refund_change));
        }

        return true;
    }

    /**
     * @return bool
     */
    public function markVoided(): bool
    {
        if ($this->isVoided() || $this->isPartiallyRefunded() || $this->isRefunded()) {
            return false;
        }

        \Illuminate\Support\Facades\Event::dispatch(new PaymentWasVoided($this));

        $this->refunded = $this->amount;
        $this->payment_status_id = PAYMENT_STATUS_VOIDED;
        $this->save();

        return true;
    }

    public function markComplete(): void
    {
        $this->payment_status_id = PAYMENT_STATUS_COMPLETED;
        $this->save();
        \Illuminate\Support\Facades\Event::dispatch(new PaymentCompleted($this));
    }

    /**
     * @param string $failureMessage
     */
    public function markFailed($failureMessage = ''): void
    {
        $this->payment_status_id = PAYMENT_STATUS_FAILED;
        $this->gateway_error = $failureMessage;
        $this->save();
        \Illuminate\Support\Facades\Event::dispatch(new PaymentFailed($this));
    }

    public function getEntityType(): string
    {
        return ENTITY_PAYMENT;
    }

    public function getCompletedAmount(): int|float
    {
        return $this->amount - $this->refunded;
    }

    public function canBeRefunded(): bool
    {
        return $this->getCompletedAmount() > 0 && ($this->isCompleted() || $this->isPartiallyRefunded());
    }

    /**
     * @return bool
     */
    public function isExchanged(): bool
    {
        return $this->exchange_currency_id || $this->exchange_rate != 1;
    }

    /**
     * @return mixed|null|stdClass|string
     */
    public function getBankDataAttribute()
    {
        if ( ! $this->routing_number) {
            return;
        }

        return PaymentMethod::lookupBankData($this->routing_number);
    }

    /**
     * @param $bank_name
     *
     * @return null
     */
    public function getBankNameAttribute($bank_name)
    {
        if ($bank_name) {
            return $bank_name;
        }

        $bankData = $this->bank_data;

        return $bankData ? $bankData->name : null;
    }

    /**
     * @param $value
     *
     * @return null|string
     */
    public function getLast4Attribute($value): ?string
    {
        return $value ? mb_str_pad($value, 4, '0', STR_PAD_LEFT) : null;
    }

    public function statusClass(): string
    {
        return static::calcStatusClass($this->payment_status_id);
    }

    public function statusLabel(): string
    {
        $amount = $this->account->formatMoney($this->refunded, $this->client);

        return static::calcStatusLabel($this->payment_status_id, $this->payment_status->name, $amount);
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

Payment::creating(function ($payment): void {});

Payment::created(function ($payment): void {
    event(new PaymentWasCreated($payment));
});
