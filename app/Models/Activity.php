<?php

namespace App\Models;

use App\Ninja\Presenters\ActivityPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class Activity.
 *
 * @property int          $id
 * @property Carbon|null  $created_at
 * @property Carbon|null  $updated_at
 * @property int          $account_id
 * @property int          $user_id
 * @property int|null     $client_id
 * @property int|null     $contact_id
 * @property int|null     $payment_id
 * @property int|null     $invoice_id
 * @property int|null     $credit_id
 * @property int|null     $invitation_id
 * @property int|null     $task_id
 * @property string|null  $json_backup
 * @property int          $activity_type_id
 * @property string|null  $adjustment
 * @property string|null  $balance
 * @property int|null     $token_id
 * @property string|null  $ip
 * @property int          $is_system
 * @property int|null     $expense_id
 * @property string|null  $notes
 * @property Account      $account
 * @property Client|null  $client
 * @property Contact|null $contact
 * @property Credit|null  $credit
 * @property Expense|null $expense
 * @property Invoice|null $invoice
 * @property Payment|null $payment
 * @property Task|null    $task
 * @property User|null    $user
 *
 * @method static Builder|Activity newModelQuery()
 * @method static Builder|Activity newQuery()
 * @method static Builder|Activity query()
 * @method static Builder|Activity scope()
 * @method static Builder|Activity whereAccountId($value)
 * @method static Builder|Activity whereActivityTypeId($value)
 * @method static Builder|Activity whereAdjustment($value)
 * @method static Builder|Activity whereBalance($value)
 * @method static Builder|Activity whereClientId($value)
 * @method static Builder|Activity whereContactId($value)
 * @method static Builder|Activity whereCreatedAt($value)
 * @method static Builder|Activity whereCreditId($value)
 * @method static Builder|Activity whereExpenseId($value)
 * @method static Builder|Activity whereId($value)
 * @method static Builder|Activity whereInvitationId($value)
 * @method static Builder|Activity whereInvoiceId($value)
 * @method static Builder|Activity whereIp($value)
 * @method static Builder|Activity whereIsSystem($value)
 * @method static Builder|Activity whereJsonBackup($value)
 * @method static Builder|Activity whereNotes($value)
 * @method static Builder|Activity wherePaymentId($value)
 * @method static Builder|Activity whereTaskId($value)
 * @method static Builder|Activity whereTokenId($value)
 * @method static Builder|Activity whereUpdatedAt($value)
 * @method static Builder|Activity whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Activity extends Model
{
    use PresentableTrait;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string
     */
    protected $presenter = ActivityPresenter::class;

    public function scopeScope($query)
    {
        return $query->whereAccountId(Auth::user()->account_id);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class)->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    public function credit()
    {
        return $this->belongsTo(Credit::class)->withTrashed();
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class)->withTrashed();
    }

    public function task()
    {
        return $this->belongsTo(Task::class)->withTrashed();
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class)->withTrashed();
    }

    public function key(): string
    {
        return sprintf('%s-%s-%s', $this->activity_type_id, $this->client_id, $this->created_at->timestamp);
    }

    public function getMessage()
    {
        $activityTypeId = $this->activity_type_id;
        $account = $this->account;
        $client = $this->client;
        $user = $this->user;
        $invoice = $this->invoice;
        $contactId = $this->contact_id;
        $contact = $this->contact;
        $payment = $this->payment;
        $credit = $this->credit;
        $expense = $this->expense;
        $isSystem = $this->is_system;
        $task = $this->task;

        $data = [
            'client'         => $client ? link_to($client->getRoute(), $client->getDisplayName()) : null,
            'user'           => $isSystem ? '<i>' . trans('texts.system') . '</i>' : e($user->getDisplayName()),
            'invoice'        => $invoice ? link_to($invoice->getRoute(), $invoice->getDisplayName()) : null,
            'quote'          => $invoice ? link_to($invoice->getRoute(), $invoice->getDisplayName()) : null,
            'contact'        => $contactId ? link_to($client->getRoute(), $contact->getDisplayName()) : e($user->getDisplayName()),
            'payment'        => $payment ? e($payment->transaction_reference) : null,
            'payment_amount' => $payment ? $account->formatMoney($payment->amount, $payment) : null,
            'adjustment'     => $this->adjustment ? $account->formatMoney($this->adjustment, $this) : null,
            'credit'         => $credit ? $account->formatMoney($credit->amount, $client) : null,
            'task'           => $task ? link_to($task->getRoute(), mb_substr($task->description, 0, 30) . '...') : null,
            'expense'        => $expense ? link_to($expense->getRoute(), mb_substr($expense->public_notes, 0, 30) . '...') : null,
        ];

        return trans('texts.activity_' . $activityTypeId, $data);
    }

    public function relatedEntityType()
    {
        switch ($this->activity_type_id) {
            case ACTIVITY_TYPE_CREATE_CLIENT:
            case ACTIVITY_TYPE_ARCHIVE_CLIENT:
            case ACTIVITY_TYPE_DELETE_CLIENT:
            case ACTIVITY_TYPE_RESTORE_CLIENT:
            case ACTIVITY_TYPE_CREATE_CREDIT:
            case ACTIVITY_TYPE_ARCHIVE_CREDIT:
            case ACTIVITY_TYPE_DELETE_CREDIT:
            case ACTIVITY_TYPE_RESTORE_CREDIT:
                return ENTITY_CLIENT;

            case ACTIVITY_TYPE_CREATE_INVOICE:
            case ACTIVITY_TYPE_UPDATE_INVOICE:
            case ACTIVITY_TYPE_EMAIL_INVOICE:
            case ACTIVITY_TYPE_VIEW_INVOICE:
            case ACTIVITY_TYPE_ARCHIVE_INVOICE:
            case ACTIVITY_TYPE_DELETE_INVOICE:
            case ACTIVITY_TYPE_RESTORE_INVOICE:
                return ENTITY_INVOICE;

            case ACTIVITY_TYPE_CREATE_PAYMENT:
            case ACTIVITY_TYPE_ARCHIVE_PAYMENT:
            case ACTIVITY_TYPE_DELETE_PAYMENT:
            case ACTIVITY_TYPE_RESTORE_PAYMENT:
            case ACTIVITY_TYPE_VOIDED_PAYMENT:
            case ACTIVITY_TYPE_REFUNDED_PAYMENT:
            case ACTIVITY_TYPE_FAILED_PAYMENT:
                return ENTITY_PAYMENT;

            case ACTIVITY_TYPE_CREATE_QUOTE:
            case ACTIVITY_TYPE_UPDATE_QUOTE:
            case ACTIVITY_TYPE_EMAIL_QUOTE:
            case ACTIVITY_TYPE_VIEW_QUOTE:
            case ACTIVITY_TYPE_ARCHIVE_QUOTE:
            case ACTIVITY_TYPE_DELETE_QUOTE:
            case ACTIVITY_TYPE_RESTORE_QUOTE:
            case ACTIVITY_TYPE_APPROVE_QUOTE:
                return ENTITY_QUOTE;

            case ACTIVITY_TYPE_CREATE_VENDOR:
            case ACTIVITY_TYPE_ARCHIVE_VENDOR:
            case ACTIVITY_TYPE_DELETE_VENDOR:
            case ACTIVITY_TYPE_RESTORE_VENDOR:
            case ACTIVITY_TYPE_CREATE_EXPENSE:
            case ACTIVITY_TYPE_ARCHIVE_EXPENSE:
            case ACTIVITY_TYPE_DELETE_EXPENSE:
            case ACTIVITY_TYPE_RESTORE_EXPENSE:
            case ACTIVITY_TYPE_UPDATE_EXPENSE:
                return ENTITY_EXPENSE;

            case ACTIVITY_TYPE_CREATE_TASK:
            case ACTIVITY_TYPE_UPDATE_TASK:
            case ACTIVITY_TYPE_ARCHIVE_TASK:
            case ACTIVITY_TYPE_DELETE_TASK:
            case ACTIVITY_TYPE_RESTORE_TASK:
                return ENTITY_TASK;
        }
    }
}
