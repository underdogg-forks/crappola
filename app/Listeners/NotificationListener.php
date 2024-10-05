<?php

namespace App\Listeners;

use App\Events\InvoiceInvitationWasViewed;
use App\Events\InvoiceWasEmailed;
use App\Events\PaymentWasCreated;
use App\Events\QuoteInvitationWasApproved;
use App\Events\QuoteInvitationWasViewed;
use App\Events\QuoteWasEmailed;
use App\Jobs\SendNotificationEmail;
use App\Jobs\SendPaymentEmail;
use App\Models\Invoice;
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Mailers\UserMailer;
use App\Notifications\PaymentCreated;
use App\Services\PushService;

/**
 * Class NotificationListener.
 */
class NotificationListener
{
    protected \App\Ninja\Mailers\UserMailer $userMailer;

    protected \App\Ninja\Mailers\ContactMailer $contactMailer;

    protected \App\Services\PushService $pushService;

    /**
     * NotificationListener constructor.
     *
     * @param UserMailer    $userMailer
     * @param ContactMailer $contactMailer
     * @param PushService   $pushService
     */
    public function __construct(UserMailer $userMailer, ContactMailer $contactMailer, PushService $pushService)
    {
        $this->userMailer = $userMailer;
        $this->contactMailer = $contactMailer;
        $this->pushService = $pushService;
    }

    /**
     * @param InvoiceWasEmailed $event
     */
    public function emailedInvoice(InvoiceWasEmailed $event): void
    {
        $this->sendNotifications($event->invoice, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->invoice, 'sent');
    }

    /**
     * @param QuoteWasEmailed $event
     */
    public function emailedQuote(QuoteWasEmailed $event): void
    {
        $this->sendNotifications($event->quote, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->quote, 'sent');
    }

    /**
     * @param InvoiceInvitationWasViewed $event
     */
    public function viewedInvoice(InvoiceInvitationWasViewed $event): void
    {
        if ( (float) ($event->invoice->balance) === 0.0) {
            return;
        }

        $this->sendNotifications($event->invoice, 'viewed');
        $this->pushService->sendNotification($event->invoice, 'viewed');
    }

    /**
     * @param QuoteInvitationWasViewed $event
     */
    public function viewedQuote(QuoteInvitationWasViewed $event): void
    {
        if ($event->quote->quote_invoice_id) {
            return;
        }

        $this->sendNotifications($event->quote, 'viewed');
        $this->pushService->sendNotification($event->quote, 'viewed');
    }

    /**
     * @param QuoteInvitationWasApproved $event
     */
    public function approvedQuote(QuoteInvitationWasApproved $event): void
    {
        $this->sendNotifications($event->quote, 'approved');
        $this->pushService->sendNotification($event->quote, 'approved');
    }

    /**
     * @param PaymentWasCreated $event
     */
    public function createdPayment(PaymentWasCreated $event): void
    {
        // only send emails for online payments
        if ( ! $event->payment->account_gateway_id) {
            return;
        }

        dispatch(new SendPaymentEmail($event->payment));
        $this->sendNotifications($event->payment->invoice, 'paid', $event->payment);

        $this->pushService->sendNotification($event->payment->invoice, 'paid');
    }

    /**
     * @param      $invoice
     * @param      $type
     * @param null $payment
     */
    private function sendNotifications(Invoice $invoice, string $type, $payment = null, $notes = false): void
    {
        foreach ($invoice->account->users as $user) {
            if ($user->{"notify_{$type}"}) {
                dispatch(new SendNotificationEmail($user, $invoice, $type, $payment, $notes));
            }

            if ($payment && $user->slack_webhook_url) {
                $user->notify(new PaymentCreated($payment, $invoice));
            }
        }
    }
}
