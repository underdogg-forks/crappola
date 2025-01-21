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
use App\Ninja\Mailers\ContactMailer;
use App\Ninja\Mailers\UserMailer;
use App\Notifications\PaymentCreated;
use App\Services\PushService;

/**
 * Class NotificationListener.
 */
class NotificationListener
{
    protected UserMailer $userMailer;

    protected ContactMailer $contactMailer;

    protected PushService $pushService;

    /**
     * NotificationListener constructor.
     */
    public function __construct(UserMailer $userMailer, ContactMailer $contactMailer, PushService $pushService)
    {
        $this->userMailer = $userMailer;
        $this->contactMailer = $contactMailer;
        $this->pushService = $pushService;
    }

    public function emailedInvoice(InvoiceWasEmailed $event): void
    {
        $this->sendNotifications($event->invoice, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->invoice, 'sent');
    }

    private function sendNotifications($invoice, string $type, $payment = null, $notes = false): void
    {
        foreach ($invoice->company->users as $user) {
            if ($user->{"notify_{$type}"}) {
                dispatch(new SendNotificationEmail($user, $invoice, $type, $payment, $notes));
            }
            if (! $payment) {
                continue;
            }
            if (! $user->slack_webhook_url) {
                continue;
            }
            $user->notify(new PaymentCreated($payment, $invoice));
        }
    }

    public function emailedQuote(QuoteWasEmailed $event): void
    {
        $this->sendNotifications($event->quote, 'sent', null, $event->notes);
        $this->pushService->sendNotification($event->quote, 'sent');
    }

    public function viewedInvoice(InvoiceInvitationWasViewed $event): void
    {
        if (! floatval($event->invoice->balance)) {
            return;
        }

        $this->sendNotifications($event->invoice, 'viewed');
        $this->pushService->sendNotification($event->invoice, 'viewed');
    }

    public function viewedQuote(QuoteInvitationWasViewed $event): void
    {
        if ($event->quote->quote_invoice_id) {
            return;
        }

        $this->sendNotifications($event->quote, 'viewed');
        $this->pushService->sendNotification($event->quote, 'viewed');
    }

    public function approvedQuote(QuoteInvitationWasApproved $event): void
    {
        $this->sendNotifications($event->quote, 'approved');
        $this->pushService->sendNotification($event->quote, 'approved');
    }

    public function createdPayment(PaymentWasCreated $event): void
    {
        // only send emails for online payments
        if (! $event->payment->account_gateway_id) {
            return;
        }

        dispatch(new SendPaymentEmail($event->payment));
        $this->sendNotifications($event->payment->invoice, 'paid', $event->payment);

        $this->pushService->sendNotification($event->payment->invoice, 'paid');
    }
}
