<?php

namespace App\Providers;

use App\Events\ClientWasArchived;
use App\Events\ClientWasCreated;
use App\Events\ClientWasDeleted;
use App\Events\ClientWasRestored;
use App\Events\ClientWasUpdated;
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
use App\Events\InvoiceItemsWereCreated;
use App\Events\InvoiceItemsWereUpdated;
use App\Events\InvoiceWasArchived;
use App\Events\InvoiceWasCreated;
use App\Events\InvoiceWasDeleted;
use App\Events\InvoiceWasEmailed;
use App\Events\InvoiceWasRestored;
use App\Events\InvoiceWasUpdated;
use App\Events\PaymentFailed;
use App\Events\PaymentWasArchived;
use App\Events\PaymentWasCreated;
use App\Events\PaymentWasDeleted;
use App\Events\PaymentWasRefunded;
use App\Events\PaymentWasRestored;
use App\Events\PaymentWasVoided;
use App\Events\ProjectWasDeleted;
use App\Events\ProposalWasDeleted;
use App\Events\QuoteInvitationWasApproved;
use App\Events\QuoteInvitationWasEmailed;
use App\Events\QuoteInvitationWasViewed;
use App\Events\QuoteItemsWereCreated;
use App\Events\QuoteItemsWereUpdated;
use App\Events\QuoteWasArchived;
use App\Events\QuoteWasCreated;
use App\Events\QuoteWasDeleted;
use App\Events\QuoteWasEmailed;
use App\Events\QuoteWasRestored;
use App\Events\QuoteWasUpdated;
use App\Events\SubdomainWasRemoved;
use App\Events\SubdomainWasUpdated;
use App\Events\TaskWasArchived;
use App\Events\TaskWasCreated;
use App\Events\TaskWasDeleted;
use App\Events\TaskWasRestored;
use App\Events\TaskWasUpdated;
use App\Events\UserLoggedIn;
use App\Events\UserSettingsChanged;
use App\Events\UserSignedUp;
use App\Events\VendorWasCreated;
use App\Events\VendorWasDeleted;
use App\Events\VendorWasUpdated;
use App\Listeners\HandleUserLoggedIn;
use App\Listeners\HandleUserSettingsChanged;
use App\Listeners\HandleUserSignedUp;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\JobExceptionOccurred;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Clients
        ClientWasCreated::class => [
            'App\Listeners\ActivityListener@createdClient',
            'App\Listeners\SubscriptionListener@createdClient',
        ],
        ClientWasArchived::class => [
            'App\Listeners\ActivityListener@archivedClient',
        ],
        ClientWasUpdated::class => [
            'App\Listeners\SubscriptionListener@updatedClient',
        ],
        ClientWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedClient',
            'App\Listeners\SubscriptionListener@deletedClient',
            'App\Listeners\HistoryListener@deletedClient',
        ],
        ClientWasRestored::class => [
            'App\Listeners\ActivityListener@restoredClient',
        ],

        // Invoices
        InvoiceWasCreated::class => [
            'App\Listeners\ActivityListener@createdInvoice',
            'App\Listeners\InvoiceListener@createdInvoice',
        ],
        InvoiceWasUpdated::class => [
            'App\Listeners\ActivityListener@updatedInvoice',
            'App\Listeners\InvoiceListener@updatedInvoice',
        ],
        InvoiceItemsWereCreated::class => [
            'App\Listeners\SubscriptionListener@createdInvoice',
        ],
        InvoiceItemsWereUpdated::class => [
            'App\Listeners\SubscriptionListener@updatedInvoice',
        ],
        InvoiceWasArchived::class => [
            'App\Listeners\ActivityListener@archivedInvoice',
        ],
        InvoiceWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedInvoice',
            'App\Listeners\TaskListener@deletedInvoice',
            'App\Listeners\ExpenseListener@deletedInvoice',
            'App\Listeners\HistoryListener@deletedInvoice',
            'App\Listeners\SubscriptionListener@deletedInvoice',
        ],
        InvoiceWasRestored::class => [
            'App\Listeners\ActivityListener@restoredInvoice',
        ],
        InvoiceWasEmailed::class => [
            'App\Listeners\InvoiceListener@emailedInvoice',
            'App\Listeners\NotificationListener@emailedInvoice',
        ],
        InvoiceInvitationWasEmailed::class => [
            'App\Listeners\ActivityListener@emailedInvoice',
        ],
        InvoiceInvitationWasViewed::class => [
            'App\Listeners\ActivityListener@viewedInvoice',
            'App\Listeners\NotificationListener@viewedInvoice',
            'App\Listeners\InvoiceListener@viewedInvoice',
        ],

        // Quotes
        QuoteWasCreated::class => [
            'App\Listeners\ActivityListener@createdQuote',
        ],
        QuoteWasUpdated::class => [
            'App\Listeners\ActivityListener@updatedQuote',
        ],
        QuoteItemsWereCreated::class => [
            'App\Listeners\SubscriptionListener@createdQuote',
        ],
        QuoteItemsWereUpdated::class => [
            'App\Listeners\SubscriptionListener@updatedQuote',
        ],
        QuoteWasArchived::class => [
            'App\Listeners\ActivityListener@archivedQuote',
        ],
        QuoteWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedQuote',
            'App\Listeners\HistoryListener@deletedQuote',
            'App\Listeners\SubscriptionListener@deletedQuote',
        ],
        QuoteWasRestored::class => [
            'App\Listeners\ActivityListener@restoredQuote',
        ],
        QuoteWasEmailed::class => [
            'App\Listeners\QuoteListener@emailedQuote',
            'App\Listeners\NotificationListener@emailedQuote',
        ],
        QuoteInvitationWasEmailed::class => [
            'App\Listeners\ActivityListener@emailedQuote',
        ],
        QuoteInvitationWasViewed::class => [
            'App\Listeners\ActivityListener@viewedQuote',
            'App\Listeners\NotificationListener@viewedQuote',
            'App\Listeners\QuoteListener@viewedQuote',
        ],
        QuoteInvitationWasApproved::class => [
            'App\Listeners\ActivityListener@approvedQuote',
            'App\Listeners\NotificationListener@approvedQuote',
            'App\Listeners\SubscriptionListener@approvedQuote',
        ],

        // Payments
        PaymentWasCreated::class => [
            'App\Listeners\ActivityListener@createdPayment',
            'App\Listeners\SubscriptionListener@createdPayment',
            'App\Listeners\InvoiceListener@createdPayment',
            'App\Listeners\NotificationListener@createdPayment',
            'App\Listeners\AnalyticsListener@trackRevenue',
        ],
        PaymentWasArchived::class => [
            'App\Listeners\ActivityListener@archivedPayment',
        ],
        PaymentWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedPayment',
            'App\Listeners\InvoiceListener@deletedPayment',
            'App\Listeners\CreditListener@deletedPayment',
            'App\Listeners\SubscriptionListener@deletedPayment',
        ],
        PaymentWasRefunded::class => [
            'App\Listeners\ActivityListener@refundedPayment',
            'App\Listeners\InvoiceListener@refundedPayment',
        ],
        PaymentWasVoided::class => [
            'App\Listeners\ActivityListener@voidedPayment',
            'App\Listeners\InvoiceListener@voidedPayment',
        ],
        PaymentFailed::class => [
            'App\Listeners\ActivityListener@failedPayment',
            'App\Listeners\InvoiceListener@failedPayment',
        ],
        PaymentWasRestored::class => [
            'App\Listeners\ActivityListener@restoredPayment',
            'App\Listeners\InvoiceListener@restoredPayment',
        ],

        // Credits
        CreditWasCreated::class => [
            'App\Listeners\ActivityListener@createdCredit',
        ],
        CreditWasArchived::class => [
            'App\Listeners\ActivityListener@archivedCredit',
        ],
        CreditWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedCredit',
        ],
        CreditWasRestored::class => [
            'App\Listeners\ActivityListener@restoredCredit',
        ],

        // User events
        UserSignedUp::class => [
            HandleUserSignedUp::class,
        ],
        UserLoggedIn::class => [
            HandleUserLoggedIn::class,
        ],
        UserSettingsChanged::class => [
            HandleUserSettingsChanged::class,
        ],

        // Task events
        TaskWasCreated::class => [
            'App\Listeners\ActivityListener@createdTask',
            'App\Listeners\SubscriptionListener@createdTask',
        ],
        TaskWasUpdated::class => [
            'App\Listeners\ActivityListener@updatedTask',
            'App\Listeners\SubscriptionListener@updatedTask',
        ],
        TaskWasRestored::class => [
            'App\Listeners\ActivityListener@restoredTask',
        ],
        TaskWasArchived::class => [
            'App\Listeners\ActivityListener@archivedTask',
        ],
        TaskWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedTask',
            'App\Listeners\SubscriptionListener@deletedTask',
            'App\Listeners\HistoryListener@deletedTask',
        ],

        // Vendor events
        VendorWasCreated::class => [
            'App\Listeners\SubscriptionListener@createdVendor',
        ],
        VendorWasUpdated::class => [
            'App\Listeners\SubscriptionListener@updatedVendor',
        ],
        VendorWasDeleted::class => [
            'App\Listeners\SubscriptionListener@deletedVendor',
        ],

        // Expense events
        ExpenseWasCreated::class => [
            'App\Listeners\ActivityListener@createdExpense',
            'App\Listeners\SubscriptionListener@createdExpense',
        ],
        ExpenseWasUpdated::class => [
            'App\Listeners\ActivityListener@updatedExpense',
            'App\Listeners\SubscriptionListener@updatedExpense',
        ],
        ExpenseWasRestored::class => [
            'App\Listeners\ActivityListener@restoredExpense',
        ],
        ExpenseWasArchived::class => [
            'App\Listeners\ActivityListener@archivedExpense',
        ],
        ExpenseWasDeleted::class => [
            'App\Listeners\ActivityListener@deletedExpense',
            'App\Listeners\SubscriptionListener@deletedExpense',
            'App\Listeners\HistoryListener@deletedExpense',
        ],

        // Project events
        ProjectWasDeleted::class => [
            'App\Listeners\HistoryListener@deletedProject',
        ],

        // Proposal events
        ProposalWasDeleted::class => [
            'App\Listeners\HistoryListener@deletedProposal',
        ],

        JobExceptionOccurred::class => [
            'App\Listeners\InvoiceListener@jobFailed',
        ],

        //DNS Add A record to Cloudflare
        SubdomainWasUpdated::class => [
            'App\Listeners\DNSListener@addDNSRecord',
        ],

        //DNS Remove A record from Cloudflare
        SubdomainWasRemoved::class => [
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
     * @param Dispatcher $events
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }
}
