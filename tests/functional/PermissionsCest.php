<?php

use Codeception\Util\Fixtures;
use Faker\Factory;

class PermissionsCest
{
    /**
     * @var \Faker\Generator
     */
    private $faker;

    private $entityArray;

    public function _before(FunctionalTester $I): void
    {
        $this->faker = Factory::create();
        $I->checkIfLogin($I);

        $this->entityArray = [
            'proposal',
            'expense',
            'project',
            'vendor',
            'product',
            'task',
            'quote',
            'credit',
            'payment',
            'contact',
            'invoice',
            'client',
            'recurring_invoice',
            'reports',
        ];
    }

    public function setViewPermissionsNothing(FunctionalTester $I): void
    {
        $I->wantTo('create a view nothing permission user');

        $permissions = [];

        $I->updateInDatabase(
            'users',
            ['is_admin'       => 0,
                'permissions' => json_encode(array_diff(array_values($permissions), [0]))
            ],
            ['email' => Fixtures::get('permissions_username')]
        );
    }

    /*
     * Test View Permissions
     *
     *  See 403 response for an individual ENTITY record
     *
     */


    public function dontViewInvoice(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewClient(FunctionalTester $I): void
    {
        $I->amOnPage('/clients/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewProduct(FunctionalTester $I): void
    {
        $I->amOnPage('/products/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewPayment(FunctionalTester $I): void
    {
        $I->amOnPage('/payments/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewQuote(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewRecurringInvoice(FunctionalTester $I): void
    {
        $I->amOnPage('/recurring_invoices/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewCredit(FunctionalTester $I): void
    {
        $I->amOnPage('/credits/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewProposal(FunctionalTester $I): void
    {
        $I->amOnPage('/proposals/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewProject(FunctionalTester $I): void
    {
        $I->amOnPage('/projects/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewTask(FunctionalTester $I): void
    {
        $I->amOnPage('/tasks/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewExpense(FunctionalTester $I): void
    {
        $I->amOnPage('/expenses/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function dontViewVendor(FunctionalTester $I): void
    {
        $I->amOnPage('/vendors/1');
        $I->canSeeInCurrentUrl('dashboard');
    }


    public function setViewPermissions(FunctionalTester $I): void
    {
        $I->wantTo('create a view only permission user');

        $permissions = [];

        foreach ($this->entityArray as $item) {
            array_push($permissions, 'view_' . $item);
        }

        $I->updateInDatabase(
            'users',
            ['is_admin'   => 0,
            'permissions' => json_encode(array_diff(array_values($permissions), [0]))
        ],
            ['email' => Fixtures::get('permissions_username')]
        );
    }


    /*
     * Test View Permissions
     *
     *  See 200 response for an individual ENTITY record
     *
     */


    public function viewInvoice(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewClient(FunctionalTester $I): void
    {
        $I->amOnPage('/clients/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewProduct(FunctionalTester $I): void
    {
        $I->amOnPage('/products/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewPayment(FunctionalTester $I): void
    {
        $I->amOnPage('/payments/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewQuote(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewRecurringInvoice(FunctionalTester $I): void
    {
        $I->amOnPage('/recurring_invoices/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewCredit(FunctionalTester $I): void
    {
        $I->amOnPage('/credits/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewProposal(FunctionalTester $I): void
    {
        $I->amOnPage('/proposals/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewProject(FunctionalTester $I): void
    {
        $I->amOnPage('/projects/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewTask(FunctionalTester $I): void
    {
        $I->amOnPage('/tasks/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewExpense(FunctionalTester $I): void
    {
        $I->amOnPage('/expenses/1');
        $I->seeResponseCodeIs(200);
    }

    public function viewVendor(FunctionalTester $I): void
    {
        $I->amOnPage('/vendors/1');
        $I->seeResponseCodeIs(200);
    }

    /*
     * Test view permissions for lists
     */


    public function viewInvoices(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/');
        $I->seeResponseCodeIs(200);
    }

    public function viewClients(FunctionalTester $I): void
    {
        $I->amOnPage('/clients/');
        $I->seeResponseCodeIs(200);
    }

    public function viewProducts(FunctionalTester $I): void
    {
        $I->amOnPage('/products/');
        $I->seeResponseCodeIs(200);
    }

    public function viewPayments(FunctionalTester $I): void
    {
        $I->amOnPage('/payments/');
        $I->seeResponseCodeIs(200);
    }

    public function viewQuotes(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/');
        $I->seeResponseCodeIs(200);
    }

    public function viewRecurringInvoices(FunctionalTester $I): void
    {
        $I->amOnPage('/recurring_invoices/');
        $I->seeResponseCodeIs(200);
    }

    public function viewCredits(FunctionalTester $I): void
    {
        $I->amOnPage('/credits/');
        $I->seeResponseCodeIs(200);
    }

    public function viewProposals(FunctionalTester $I): void
    {
        $I->amOnPage('/proposals/');
        $I->seeResponseCodeIs(200);
    }

    public function viewProjects(FunctionalTester $I): void
    {
        $I->amOnPage('/projects/');
        $I->seeResponseCodeIs(200);
    }

    public function viewTasks(FunctionalTester $I): void
    {
        $I->amOnPage('/tasks/');
        $I->seeResponseCodeIs(200);
    }

    public function viewExpenses(FunctionalTester $I): void
    {
        $I->amOnPage('/expenses/');
        $I->seeResponseCodeIs(200);
    }

    public function viewVendors(FunctionalTester $I): void
    {
        $I->amOnPage('/vendors/');
        $I->seeResponseCodeIs(200);
    }

    /*
     * Test Create permissions when only VIEW enabled
     */


    public function createClient(FunctionalTester $I): void
    {
        $I->amOnPage('/clients/create');
        $I->seeResponseCodeIs(403);
    }

    public function createInvoice(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/create');
        $I->seeResponseCodeIs(403);
    }

    public function createProduct(FunctionalTester $I): void
    {
        $I->amOnPage('/products/create');
        $I->seeResponseCodeIs(403);
    }

    public function createPayment(FunctionalTester $I): void
    {
        $I->amOnPage('/payments/create');
        $I->seeResponseCodeIs(403);
    }

    public function createQuote(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/create');
        $I->seeResponseCodeIs(403);
    }

    public function createRecurringInvoice(FunctionalTester $I): void
    {
        $I->amOnPage('/recurring_invoices/create');
        $I->seeResponseCodeIs(403);
    }

    public function createCredit(FunctionalTester $I): void
    {
        $I->amOnPage('/credits/create');
        $I->seeResponseCodeIs(403);
    }

    public function createProposal(FunctionalTester $I): void
    {
        $I->amOnPage('/proposals/create');
        $I->seeResponseCodeIs(403);
    }

    public function createProject(FunctionalTester $I): void
    {
        $I->amOnPage('/projects/create');
        $I->seeResponseCodeIs(403);
    }

    public function createTask(FunctionalTester $I): void
    {
        $I->amOnPage('/tasks/create');
        $I->seeResponseCodeIs(403);
    }

    public function createExpense(FunctionalTester $I): void
    {
        $I->amOnPage('/expenses/create');
        $I->seeResponseCodeIs(403);
    }

    public function createVendor(FunctionalTester $I): void
    {
        $I->amOnPage('/vendors/create');
        $I->seeResponseCodeIs(403);
    }


    /****
     *  Test the edge case with Invoice and Quote Permissions
     */

    public function setQuoteOnlyPermissions(FunctionalTester $I): void
    {
        $I->wantTo('create a quote view only permission user');

        $permissions = [];

        array_push($permissions, 'view_quote');
        array_push($permissions, 'edit_quote');
        array_push($permissions, 'create_quote');

        $I->updateInDatabase(
            'users',
            ['is_admin'       => 0,
                'permissions' => json_encode(array_diff(array_values($permissions), [0]))
            ],
            ['email' => Fixtures::get('permissions_username')]
        );
    }

    public function testCreateInvoice(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/create');
        $I->seeResponseCodeIs(403);
    }

    public function testViewInvoice(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/1');
        $I->canSeeInCurrentUrl('dashboard');
    }

    public function testEditInvoice(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/11/edit');
        $I->canSeeInCurrentUrl('dashboard');
    }



    public function testCreateQuote(FunctionalTester $I): void
    {
        $I->amOnPage('/quotes/create');
        $I->seeResponseCodeIs(200);
    }

    public function testEditQuote(FunctionalTester $I): void
    {
        $I->amOnPage('/quotes/1/edit');
        $I->seeResponseCodeIs(200);
    }

    public function testViewQuote(FunctionalTester $I): void
    {
        $I->amOnPage('/quotes/1');
        $I->seeResponseCodeIs(200);
    }

    public function setInvoiceOnlyPermissions(FunctionalTester $I): void
    {
        $I->wantTo('create a invoice view only permission user');

        $permissions = [];

        array_push($permissions, 'view_invoice');
        array_push($permissions, 'edit_invoice');
        array_push($permissions, 'create_invoice');

        $I->updateInDatabase(
            'users',
            ['is_admin'       => 0,
                'permissions' => json_encode(array_diff(array_values($permissions), [0]))
            ],
            ['email' => Fixtures::get('permissions_username')]
        );
    }


    public function testCreateInvoiceOnly(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/create');
        $I->seeResponseCodeIs(200);
    }

    public function testViewInvoiceOnly(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/1');
        $I->seeResponseCodeIs(200);
    }

    public function testEditInvoiceOnly(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/1/edit');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateQuoteOnly(FunctionalTester $I): void
    {
        $I->amOnPage('/quotes/create');
        $I->seeResponseCodeIs(403);
    }

    public function testEditQuoteOnly(FunctionalTester $I): void
    {
        $I->amOnPage('/quotes/1/edit');
        $I->seeResponseCodeIs(403);
    }

    public function testViewQuoteOnly(FunctionalTester $I): void
    {
        $I->amOnPage('/quotes/1');
        $I->seeResponseCodeIs(403);
    }


    public function setCreatePermissions(FunctionalTester $I): void
    {
        $I->wantTo('make a create only permission user');

        $permissions = [];

        foreach ($this->entityArray as $item) {
            array_push($permissions, 'create_' . $item);
        }

        $I->updateInDatabase(
            'users',
            ['is_admin'       => 0,
                'permissions' => json_encode(array_diff(array_values($permissions), [0]))
            ],
            ['email' => Fixtures::get('permissions_username')]
        );
    }

    public function testCreateInvoiceCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/invoices/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateProposalCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/proposals/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateProposalSnippetCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/proposals/snippets/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateProposalTemplateCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/proposals/templates/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateExpenseCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/expenses/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateProjectCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/projects/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateVendorCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/vendors/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateProductCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/products/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateTasksCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/tasks/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateQuotesCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/quotes/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateCreditsCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/credits/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreatePaymentsCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/payments/create');
        $I->seeResponseCodeIs(200);
    }

    public function testCreateClientsCreateOnlyPermissions(FunctionalTester $I): void
    {
        $I->amOnPage('/clients/create');
        $I->seeResponseCodeIs(200);
    }
}
