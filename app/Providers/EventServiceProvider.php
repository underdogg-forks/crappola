<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Clients
        \App\Events\ClientWasCreated::class => [
            'App\Listeners\ActivityListener@createdClient',
            'App\Listeners\SubscriptionListener@createdClient',
        ],
        \App\Events\ClientWasArchived::class => [
            'App\Listeners\ActivityListener@archivedClient',
        ],
        \App\Events\ClientWasUpdated::class => [
            'App\Listeners\SubscriptionListener@updatedClient',
        ],
        \App\Events\ClientWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedClient',
            'App\Listeners\SubscriptionListener@deletedClient',
            'App\Listeners\HistoryListener@deletedClient',
        ],
        \App\Events\ClientWasRestored::class => [
            'App\Listeners\ActivityListener@restoredClient',
        ],

        // Invoices
        \App\Events\InvoiceWasCreated::class => [
            'App\Listeners\ActivityListener@createdInvoice',
            'App\Listeners\InvoiceListener@createdInvoice',
        ],
        \App\Events\InvoiceWasUpdated::class => [
            'App\Listeners\ActivityListener@updatedInvoice',
            'App\Listeners\InvoiceListener@updatedInvoice',
        ],
        \App\Events\InvoiceItemsWereCreated::class => [
            'App\Listeners\SubscriptionListener@createdInvoice',
        ],
        \App\Events\InvoiceItemsWereUpdated::class => [
            'App\Listeners\SubscriptionListener@updatedInvoice',
        ],
        \App\Events\InvoiceWasArchived::class => [
            'App\Listeners\ActivityListener@archivedInvoice',
        ],
        \App\Events\InvoiceWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedInvoice',
            'App\Listeners\TaskListener@deletedInvoice',
            'App\Listeners\ExpenseListener@deletedInvoice',
            'App\Listeners\HistoryListener@deletedInvoice',
            'App\Listeners\SubscriptionListener@deletedInvoice',
        ],
        \App\Events\InvoiceWasRestored::class => [
            'App\Listeners\ActivityListener@restoredInvoice',
        ],
        \App\Events\InvoiceWasEmailed::class => [
            'App\Listeners\InvoiceListener@emailedInvoice',
            'App\Listeners\NotificationListener@emailedInvoice',
        ],
        \App\Events\InvoiceInvitationWasEmailed::class => [
            'App\Listeners\ActivityListener@emailedInvoice',
        ],
        \App\Events\InvoiceInvitationWasViewed::class => [
            'App\Listeners\ActivityListener@viewedInvoice',
            'App\Listeners\NotificationListener@viewedInvoice',
            'App\Listeners\InvoiceListener@viewedInvoice',
        ],

        // Quotes
        \App\Events\QuoteWasCreated::class => [
            'App\Listeners\ActivityListener@createdQuote',
        ],
        \App\Events\QuoteWasUpdated::class => [
            'App\Listeners\ActivityListener@updatedQuote',
        ],
        \App\Events\QuoteItemsWereCreated::class => [
            'App\Listeners\SubscriptionListener@createdQuote',
        ],
        \App\Events\QuoteItemsWereUpdated::class => [
            'App\Listeners\SubscriptionListener@updatedQuote',
        ],
        \App\Events\QuoteWasArchived::class => [
            'App\Listeners\ActivityListener@archivedQuote',
        ],
        \App\Events\QuoteWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedQuote',
            'App\Listeners\HistoryListener@deletedQuote',
            'App\Listeners\SubscriptionListener@deletedQuote',
        ],
        \App\Events\QuoteWasRestored::class => [
            'App\Listeners\ActivityListener@restoredQuote',
        ],
        \App\Events\QuoteWasEmailed::class => [
            'App\Listeners\QuoteListener@emailedQuote',
            'App\Listeners\NotificationListener@emailedQuote',
        ],
        \App\Events\QuoteInvitationWasEmailed::class => [
            'App\Listeners\ActivityListener@emailedQuote',
        ],
        \App\Events\QuoteInvitationWasViewed::class => [
            'App\Listeners\ActivityListener@viewedQuote',
            'App\Listeners\NotificationListener@viewedQuote',
            'App\Listeners\QuoteListener@viewedQuote',
        ],
        \App\Events\QuoteInvitationWasApproved::class => [
            'App\Listeners\ActivityListener@approvedQuote',
            'App\Listeners\NotificationListener@approvedQuote',
            'App\Listeners\SubscriptionListener@approvedQuote',
        ],

        // Payments
        \App\Events\PaymentWasCreated::class => [
            'App\Listeners\ActivityListener@createdPayment',
            'App\Listeners\SubscriptionListener@createdPayment',
            'App\Listeners\InvoiceListener@createdPayment',
            'App\Listeners\NotificationListener@createdPayment',
            'App\Listeners\AnalyticsListener@trackRevenue',
        ],
        \App\Events\PaymentWasArchived::class => [
            'App\Listeners\ActivityListener@archivedPayment',
        ],
        \App\Events\PaymentWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedPayment',
            'App\Listeners\InvoiceListener@deletedPayment',
            'App\Listeners\CreditListener@deletedPayment',
            'App\Listeners\SubscriptionListener@deletedPayment',
        ],
        \App\Events\PaymentWasRefunded::class => [
            'App\Listeners\ActivityListener@refundedPayment',
            'App\Listeners\InvoiceListener@refundedPayment',
        ],
        \App\Events\PaymentWasVoided::class => [
            'App\Listeners\ActivityListener@voidedPayment',
            'App\Listeners\InvoiceListener@voidedPayment',
        ],
        \App\Events\PaymentFailed::class => [
            'App\Listeners\ActivityListener@failedPayment',
            'App\Listeners\InvoiceListener@failedPayment',
        ],
        \App\Events\PaymentWasRestored::class => [
            'App\Listeners\ActivityListener@restoredPayment',
            'App\Listeners\InvoiceListener@restoredPayment',
        ],

        // Credits
        \App\Events\CreditWasCreated::class => [
            'App\Listeners\ActivityListener@createdCredit',
        ],
        \App\Events\CreditWasArchived::class => [
            'App\Listeners\ActivityListener@archivedCredit',
        ],
        \App\Events\CreditWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedCredit',
        ],
        \App\Events\CreditWasRestored::class => [
            'App\Listeners\ActivityListener@restoredCredit',
        ],

        // User events
        \App\Events\UserSignedUp::class => [
            \App\Listeners\HandleUserSignedUp::class,
        ],
        \App\Events\UserLoggedIn::class => [
            \App\Listeners\HandleUserLoggedIn::class,
        ],
        \App\Events\UserSettingsChanged::class => [
            \App\Listeners\HandleUserSettingsChanged::class,
        ],

        // Task events
        \App\Events\TaskWasCreated::class => [
            'App\Listeners\ActivityListener@createdTask',
            'App\Listeners\SubscriptionListener@createdTask',
        ],
        \App\Events\TaskWasUpdated::class => [
            'App\Listeners\ActivityListener@updatedTask',
            'App\Listeners\SubscriptionListener@updatedTask',
        ],
        \App\Events\TaskWasRestored::class => [
            'App\Listeners\ActivityListener@restoredTask',
        ],
        \App\Events\TaskWasArchived::class => [
            'App\Listeners\ActivityListener@archivedTask',
        ],
        \App\Events\TaskWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedTask',
            'App\Listeners\SubscriptionListener@deletedTask',
            'App\Listeners\HistoryListener@deletedTask',
        ],

        // Vendor events
        \App\Events\VendorWasCreated::class => [
            'App\Listeners\SubscriptionListener@createdVendor',
        ],
        \App\Events\VendorWasUpdated::class => [
            'App\Listeners\SubscriptionListener@updatedVendor',
        ],
        \App\Events\VendorWasDeleted::class => [
            'App\Listeners\SubscriptionListener@deletedVendor',
        ],

        // Expense events
        \App\Events\ExpenseWasCreated::class => [
            'App\Listeners\ActivityListener@createdExpense',
            'App\Listeners\SubscriptionListener@createdExpense',
        ],
        \App\Events\ExpenseWasUpdated::class => [
            'App\Listeners\ActivityListener@updatedExpense',
            'App\Listeners\SubscriptionListener@updatedExpense',
        ],
        \App\Events\ExpenseWasRestored::class => [
            'App\Listeners\ActivityListener@restoredExpense',
        ],
        \App\Events\ExpenseWasArchived::class => [
            'App\Listeners\ActivityListener@archivedExpense',
        ],
        \App\Events\ExpenseWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedExpense',
            'App\Listeners\SubscriptionListener@deletedExpense',
            'App\Listeners\HistoryListener@deletedExpense',
        ],

        // Project events
        \App\Events\ProjectWasDeleted::class => [
            'App\Listeners\HistoryListener@deletedProject',
        ],

        // Proposal events
        \App\Events\ProposalWasDeleted::class => [
            'App\Listeners\HistoryListener@deletedProposal',
        ],

        \Illuminate\Queue\Events\JobExceptionOccurred::class => [
            'App\Listeners\InvoiceListener@jobFailed',
        ],

        //DNS Add A record to Cloudflare
        \App\Events\SubdomainWasUpdated::class => [
            'App\Listeners\DNSListener@addDNSRecord',
        ],

        //DNS Remove A record from Cloudflare
        \App\Events\SubdomainWasRemoved::class => [
            'App\Listeners\DNSListener@removeDNSRecord',
        ],

        /*
        // Update events
        \Codedge\Updater\Events\UpdateAvailable::class => [
            \Codedge\Updater\Listeners\SendUpdateAvailableNotification::class,
        ],
        */
    ];

    /**
     * Register any other events for your application.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }
}
