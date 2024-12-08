<?php

namespace App\Providers;

use App\Models\AccountGateway;
use App\Models\AccountGatewayToken;
use App\Models\AccountToken;
use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Credit;
use App\Models\Document;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentTerm;
use App\Models\Product;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\ProposalCategory;
use App\Models\ProposalSnippet;
use App\Models\ProposalTemplate;
use App\Models\RecurringExpense;
use App\Models\Subscription;
use App\Models\Task;
use App\Models\TaxRate;
use App\Models\Vendor;
use App\Policies\AccountGatewayPolicy;
use App\Policies\BankAccountPolicy;
use App\Policies\ClientPolicy;
use App\Policies\ContactPolicy;
use App\Policies\CreditPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\ExpenseCategoryPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\GenericEntityPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PaymentTermPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\ProposalCategoryPolicy;
use App\Policies\ProposalPolicy;
use App\Policies\ProposalSnippetPolicy;
use App\Policies\ProposalTemplatePolicy;
use App\Policies\RecurringExpensePolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TaxRatePolicy;
use App\Policies\TokenPolicy;
use App\Policies\VendorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Client::class              => ClientPolicy::class,
        Contact::class             => ContactPolicy::class,
        Credit::class              => CreditPolicy::class,
        Document::class            => DocumentPolicy::class,
        Expense::class             => ExpensePolicy::class,
        RecurringExpense::class    => RecurringExpensePolicy::class,
        ExpenseCategory::class     => ExpenseCategoryPolicy::class,
        Invoice::class             => InvoicePolicy::class,
        Payment::class             => PaymentPolicy::class,
        Task::class                => TaskPolicy::class,
        Vendor::class              => VendorPolicy::class,
        Product::class             => ProductPolicy::class,
        TaxRate::class             => TaxRatePolicy::class,
        AccountGateway::class      => AccountGatewayPolicy::class,
        AccountToken::class        => TokenPolicy::class,
        Subscription::class        => SubscriptionPolicy::class,
        BankAccount::class         => BankAccountPolicy::class,
        PaymentTerm::class         => PaymentTermPolicy::class,
        Project::class             => ProjectPolicy::class,
        AccountGatewayToken::class => CustomerPolicy::class,
        Proposal::class            => ProposalPolicy::class,
        ProposalSnippet::class     => ProposalSnippetPolicy::class,
        ProposalTemplate::class    => ProposalTemplatePolicy::class,
        ProposalCategory::class    => ProposalCategoryPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param \Illuminate\Contracts\Auth\Access\Gate $gate
     *
     * @return void
     */
    public function boot(): void
    {
        foreach (get_class_methods(new GenericEntityPolicy()) as $method) {
            Gate::define($method, 'App\Policies\GenericEntityPolicy@' . $method);
        }

        $this->registerPolicies();
    }
}
