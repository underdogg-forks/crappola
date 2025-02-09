<?php

namespace App\Models;

use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Events\PaymentWasCreated;
use App\Events\PaymentWasRefunded;
use App\Events\PaymentWasVoided;
use App\Ninja\Presenters\PaymentPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Laracasts\Presenter\PresentableTrait;
use stdClass;

/**
 * Class Payment.
 *
 * @property int                        $id
 * @property int                        $invoice_id
 * @property int                        $account_id
 * @property int                        $client_id
 * @property int|null                   $contact_id
 * @property int|null                   $invitation_id
 * @property int|null                   $user_id
 * @property int|null                   $account_gateway_id
 * @property int|null                   $payment_type_id
 * @property Carbon|null                $created_at
 * @property Carbon|null                $updated_at
 * @property Carbon|null                $deleted_at
 * @property int                        $is_deleted
 * @property string                     $amount
 * @property string|null                $payment_date
 * @property string|null                $transaction_reference
 * @property string|null                $payer_id
 * @property int                        $public_id
 * @property string                     $refunded
 * @property int                        $payment_status_id
 * @property int|null                   $routing_number
 * @property null|string                $last4
 * @property string|null                $expiration
 * @property string|null                $gateway_error
 * @property string|null                $email
 * @property int|null                   $payment_method_id
 * @property null                       $bank_name
 * @property string|null                $ip
 * @property string|null                $credit_ids
 * @property string|null                $private_notes
 * @property string                     $exchange_rate
 * @property int                        $exchange_currency_id
 * @property Account                    $account
 * @property AccountGateway|null        $account_gateway
 * @property Client                     $client
 * @property Contact|null               $contact
 * @property mixed|null|stdClass|string $bank_data
 * @property Invitation|null            $invitation
 * @property Invoice                    $invoice
 * @property PaymentMethod|null         $payment_method
 * @property PaymentStatus              $payment_status
 * @property PaymentType|null           $payment_type
 * @property User|null                  $user
 *
 * @method static Builder|Payment dateRange($startDate, $endDate)
 * @method static Builder|Payment excludeFailed()
 * @method static Builder|Payment newModelQuery()
 * @method static Builder|Payment newQuery()
 * @method static Builder|Payment onlyTrashed()
 * @method static Builder|Payment query()
 * @method static Builder|Payment scope(bool $publicId = false, bool $accountId = false)
 * @method static Builder|Payment whereAccountGatewayId($value)
 * @method static Builder|Payment whereAccountId($value)
 * @method static Builder|Payment whereAmount($value)
 * @method static Builder|Payment whereBankName($value)
 * @method static Builder|Payment whereClientId($value)
 * @method static Builder|Payment whereContactId($value)
 * @method static Builder|Payment whereCreatedAt($value)
 * @method static Builder|Payment whereCreditIds($value)
 * @method static Builder|Payment whereDeletedAt($value)
 * @method static Builder|Payment whereEmail($value)
 * @method static Builder|Payment whereExchangeCurrencyId($value)
 * @method static Builder|Payment whereExchangeRate($value)
 * @method static Builder|Payment whereExpiration($value)
 * @method static Builder|Payment whereGatewayError($value)
 * @method static Builder|Payment whereId($value)
 * @method static Builder|Payment whereInvitationId($value)
 * @method static Builder|Payment whereInvoiceId($value)
 * @method static Builder|Payment whereIp($value)
 * @method static Builder|Payment whereIsDeleted($value)
 * @method static Builder|Payment whereLast4($value)
 * @method static Builder|Payment wherePayerId($value)
 * @method static Builder|Payment wherePaymentDate($value)
 * @method static Builder|Payment wherePaymentMethodId($value)
 * @method static Builder|Payment wherePaymentStatusId($value)
 * @method static Builder|Payment wherePaymentTypeId($value)
 * @method static Builder|Payment wherePrivateNotes($value)
 * @method static Builder|Payment wherePublicId($value)
 * @method static Builder|Payment whereRefunded($value)
 * @method static Builder|Payment whereRoutingNumber($value)
 * @method static Builder|Payment whereTransactionReference($value)
 * @method static Builder|Payment whereUpdatedAt($value)
 * @method static Builder|Payment whereUserId($value)
 * @method static Builder|Payment withActiveOrSelected($id = false)
 * @method static Builder|Payment withArchived()
 * @method static Builder|Payment withTrashed()
 * @method static Builder|Payment withoutTrashed()
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
    protected $presenter = PaymentPresenter::class;

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
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class)->withTrashed();
    }

    public function account_gateway()
    {
        return $this->belongsTo(AccountGateway::class)->withTrashed();
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function payment_status()
    {
        return $this->belongsTo(PaymentStatus::class);
    }

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

    public function isPending(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_PENDING;
    }

    public function isFailed(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_FAILED;
    }

    public function isCompleted(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_COMPLETED;
    }

    public function isPartiallyRefunded(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_PARTIALLY_REFUNDED;
    }

    public function isRefunded(): bool
    {
        return $this->payment_status_id == PAYMENT_STATUS_REFUNDED;
    }

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

            Event::dispatch(new PaymentWasRefunded($this, $refund_change));
        }

        return true;
    }

    public function markVoided(): bool
    {
        if ($this->isVoided() || $this->isPartiallyRefunded() || $this->isRefunded()) {
            return false;
        }

        Event::dispatch(new PaymentWasVoided($this));

        $this->refunded = $this->amount;
        $this->payment_status_id = PAYMENT_STATUS_VOIDED;
        $this->save();

        return true;
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
            return null;
        }

        return PaymentMethod::lookupBankData($this->routing_number);
    }

    /**
     * @param $bank_name
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
            ->first(['json_backup']);

        return $activity->json_backup;
    }
}

Payment::creating(function ($payment): void {});

Payment::created(function ($payment): void {
    event(new PaymentWasCreated($payment));
});
