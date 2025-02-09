<?php

namespace App\Listeners;

use App\Events\ClientWasArchived;
use App\Events\ClientWasCreated;
use App\Events\ClientWasDeleted;
use App\Events\ClientWasRestored;
use App\Events\CreditWasArchived;
use App\Events\CreditWasCreated;
use App\Events\CreditWasDeleted;
use App\Events\CreditWasRestored;
use App\Events\ExpenseWasArchived;
use App\Events\ExpenseWasCreated;
use App\Events\ExpenseWasDeleted;
use App\Events\ExpenseWasRestored;
use App\Events\ExpenseWasUpdated;
use App\Events\InvoiceInvitationWasEmailed;
use App\Events\InvoiceInvitationWasViewed;
use App\Events\InvoiceWasArchived;
use App\Events\InvoiceWasCreated;
use App\Events\InvoiceWasDeleted;
use App\Events\InvoiceWasRestored;
use App\Events\InvoiceWasUpdated;
use App\Events\PaymentFailed;
use App\Events\PaymentWasArchived;
use App\Events\PaymentWasCreated;
use App\Events\PaymentWasDeleted;
use App\Events\PaymentWasRefunded;
use App\Events\PaymentWasRestored;
use App\Events\PaymentWasVoided;
use App\Events\QuoteInvitationWasApproved;
use App\Events\QuoteInvitationWasEmailed;
use App\Events\QuoteInvitationWasViewed;
use App\Events\QuoteWasArchived;
use App\Events\QuoteWasCreated;
use App\Events\QuoteWasDeleted;
use App\Events\QuoteWasRestored;
use App\Events\QuoteWasUpdated;
use App\Events\TaskWasArchived;
use App\Events\TaskWasCreated;
use App\Events\TaskWasDeleted;
use App\Events\TaskWasRestored;
use App\Events\TaskWasUpdated;
use App\Models\Invoice;
use App\Ninja\Repositories\ActivityRepository;
use Illuminate\Support\Facades\App;

/**
 * Class ActivityListener.
 */
class ActivityListener
{
    protected ActivityRepository $activityRepo;

    /**
     * ActivityListener constructor.
     */
    public function __construct(ActivityRepository $activityRepo)
    {
        $this->activityRepo = $activityRepo;
    }

    public function createdClient(ClientWasCreated $event): void
    {
        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_CREATE_CLIENT
        );
    }

    public function deletedClient(ClientWasDeleted $event): void
    {
        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_DELETE_CLIENT
        );
    }

    public function archivedClient(ClientWasArchived $event): void
    {
        if ($event->client->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_ARCHIVE_CLIENT
        );
    }

    public function restoredClient(ClientWasRestored $event): void
    {
        $this->activityRepo->create(
            $event->client,
            ACTIVITY_TYPE_RESTORE_CLIENT
        );
    }

    public function createdInvoice(InvoiceWasCreated $event): void
    {
        $this->activityRepo->create(
            $event->invoice,
            ACTIVITY_TYPE_CREATE_INVOICE,
            $event->invoice->getAdjustment()
        );
    }

    public function updatedInvoice(InvoiceWasUpdated $event): void
    {
        if ( ! $event->invoice->isChanged()) {
            return;
        }

        $backupInvoice = Invoice::with('invoice_items', 'client.account', 'client.contacts')
            ->withTrashed()
            ->find($event->invoice->id);

        $activity = $this->activityRepo->create(
            $event->invoice,
            ACTIVITY_TYPE_UPDATE_INVOICE,
            $event->invoice->getAdjustment()
        );

        $activity->json_backup = $backupInvoice->hidePrivateFields()->toJSON();
        $activity->save();
    }

    public function deletedInvoice(InvoiceWasDeleted $event): void
    {
        $invoice = $event->invoice;

        $this->activityRepo->create(
            $invoice,
            ACTIVITY_TYPE_DELETE_INVOICE,
            $invoice->affectsBalance() ? $invoice->balance * -1 : 0,
            $invoice->affectsBalance() ? $invoice->getAmountPaid() * -1 : 0
        );
    }

    public function archivedInvoice(InvoiceWasArchived $event): void
    {
        if ($event->invoice->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->invoice,
            ACTIVITY_TYPE_ARCHIVE_INVOICE
        );
    }

    public function restoredInvoice(InvoiceWasRestored $event): void
    {
        $invoice = $event->invoice;

        $this->activityRepo->create(
            $invoice,
            ACTIVITY_TYPE_RESTORE_INVOICE,
            $invoice->affectsBalance() && $event->fromDeleted ? $invoice->balance : 0,
            $invoice->affectsBalance() && $event->fromDeleted ? $invoice->getAmountPaid() : 0
        );
    }

    public function emailedInvoice(InvoiceInvitationWasEmailed $event): void
    {
        $this->activityRepo->create(
            $event->invitation->invoice,
            ACTIVITY_TYPE_EMAIL_INVOICE,
            false,
            false,
            $event->invitation,
            $event->notes
        );
    }

    public function viewedInvoice(InvoiceInvitationWasViewed $event): void
    {
        $this->activityRepo->create(
            $event->invoice,
            ACTIVITY_TYPE_VIEW_INVOICE,
            false,
            false,
            $event->invitation
        );
    }

    public function createdQuote(QuoteWasCreated $event): void
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_CREATE_QUOTE
        );
    }

    public function updatedQuote(QuoteWasUpdated $event): void
    {
        if ( ! $event->quote->isChanged()) {
            return;
        }

        $backupQuote = Invoice::with('invoice_items', 'client.account', 'client.contacts')
            ->withTrashed()
            ->find($event->quote->id);

        $activity = $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_UPDATE_QUOTE
        );

        $activity->json_backup = $backupQuote->hidePrivateFields()->toJSON();
        $activity->save();
    }

    public function deletedQuote(QuoteWasDeleted $event): void
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_DELETE_QUOTE
        );
    }

    public function archivedQuote(QuoteWasArchived $event): void
    {
        if ($event->quote->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_ARCHIVE_QUOTE
        );
    }

    public function restoredQuote(QuoteWasRestored $event): void
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_RESTORE_QUOTE
        );
    }

    public function emailedQuote(QuoteInvitationWasEmailed $event): void
    {
        $this->activityRepo->create(
            $event->invitation->invoice,
            ACTIVITY_TYPE_EMAIL_QUOTE,
            false,
            false,
            $event->invitation,
            $event->notes
        );
    }

    public function viewedQuote(QuoteInvitationWasViewed $event): void
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_VIEW_QUOTE,
            false,
            false,
            $event->invitation
        );
    }

    public function approvedQuote(QuoteInvitationWasApproved $event): void
    {
        $this->activityRepo->create(
            $event->quote,
            ACTIVITY_TYPE_APPROVE_QUOTE,
            false,
            false,
            $event->invitation
        );
    }

    public function createdCredit(CreditWasCreated $event): void
    {
        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_CREATE_CREDIT
        );
    }

    public function deletedCredit(CreditWasDeleted $event): void
    {
        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_DELETE_CREDIT
        );
    }

    public function archivedCredit(CreditWasArchived $event): void
    {
        if ($event->credit->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_ARCHIVE_CREDIT
        );
    }

    public function restoredCredit(CreditWasRestored $event): void
    {
        $this->activityRepo->create(
            $event->credit,
            ACTIVITY_TYPE_RESTORE_CREDIT
        );
    }

    public function createdPayment(PaymentWasCreated $event): void
    {
        $this->activityRepo->create(
            $event->payment,
            ACTIVITY_TYPE_CREATE_PAYMENT,
            $event->payment->amount * -1,
            $event->payment->amount,
            false,
            App::runningInConsole() ? 'auto_billed' : ''
        );
    }

    public function deletedPayment(PaymentWasDeleted $event): void
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_DELETE_PAYMENT,
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount(),
            $payment->isFailedOrVoided() ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function refundedPayment(PaymentWasRefunded $event): void
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_REFUNDED_PAYMENT,
            $event->refundAmount,
            $event->refundAmount * -1
        );
    }

    public function voidedPayment(PaymentWasVoided $event): void
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_VOIDED_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function failedPayment(PaymentFailed $event): void
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_FAILED_PAYMENT,
            $payment->is_deleted ? 0 : $payment->getCompletedAmount(),
            $payment->is_deleted ? 0 : $payment->getCompletedAmount() * -1
        );
    }

    public function archivedPayment(PaymentWasArchived $event): void
    {
        if ($event->payment->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->payment,
            ACTIVITY_TYPE_ARCHIVE_PAYMENT
        );
    }

    public function restoredPayment(PaymentWasRestored $event): void
    {
        $payment = $event->payment;

        $this->activityRepo->create(
            $payment,
            ACTIVITY_TYPE_RESTORE_PAYMENT,
            $event->fromDeleted && ! $payment->isFailedOrVoided() ? $payment->getCompletedAmount() * -1 : 0,
            $event->fromDeleted && ! $payment->isFailedOrVoided() ? $payment->getCompletedAmount() : 0
        );
    }

    /**
     * Creates an activity when a task was created.
     */
    public function createdTask(TaskWasCreated $event): void
    {
        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_CREATE_TASK
        );
    }

    /**
     * Creates an activity when a task was updated.
     */
    public function updatedTask(TaskWasUpdated $event): void
    {
        if ( ! $event->task->isChanged()) {
            return;
        }

        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_UPDATE_TASK
        );
    }

    public function archivedTask(TaskWasArchived $event): void
    {
        if ($event->task->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_ARCHIVE_TASK
        );
    }

    public function deletedTask(TaskWasDeleted $event): void
    {
        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_DELETE_TASK
        );
    }

    public function restoredTask(TaskWasRestored $event): void
    {
        $this->activityRepo->create(
            $event->task,
            ACTIVITY_TYPE_RESTORE_TASK
        );
    }

    public function createdExpense(ExpenseWasCreated $event): void
    {
        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_CREATE_EXPENSE
        );
    }

    public function updatedExpense(ExpenseWasUpdated $event): void
    {
        if ( ! $event->expense->isChanged()) {
            return;
        }

        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_UPDATE_EXPENSE
        );
    }

    public function archivedExpense(ExpenseWasArchived $event): void
    {
        if ($event->expense->is_deleted) {
            return;
        }

        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_ARCHIVE_EXPENSE
        );
    }

    public function deletedExpense(ExpenseWasDeleted $event): void
    {
        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_DELETE_EXPENSE
        );
    }

    public function restoredExpense(ExpenseWasRestored $event): void
    {
        $this->activityRepo->create(
            $event->expense,
            ACTIVITY_TYPE_RESTORE_EXPENSE
        );
    }
}
