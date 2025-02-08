<?php

namespace App\Console\Commands;

use App\Libraries\Utils;
use App\Models\ExpenseCategory;
use App\Models\Project;
use App\Models\TaxRate;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketTemplate;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Repositories\ClientRepository;
use App\Ninja\Repositories\ExpenseRepository;
use App\Ninja\Repositories\InvoiceRepository;
use App\Ninja\Repositories\PaymentRepository;
use App\Ninja\Repositories\ProjectRepository;
use App\Ninja\Repositories\TaskRepository;
use App\Ninja\Repositories\TicketRepository;
use App\Ninja\Repositories\VendorRepository;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

/**
 * Class CreateTestData.
 */
class CreateTestData extends Command
{
    /**
     * @var string
     */
    protected $description = 'Create Test Data';

    /**
     * @var string
     */
    protected $signature = 'ninja:create-test-data {count=1} {create_account=false} {--database}';

    protected $token;

    /**
     * CreateTestData constructor.
     */
    public function __construct(
        TicketRepository $ticketRepo,
        ClientRepository $clientRepo,
        InvoiceRepository $invoiceRepo,
        PaymentRepository $paymentRepo,
        VendorRepository $vendorRepo,
        ExpenseRepository $expenseRepo,
        TaskRepository $taskRepo,
        ProjectRepository $projectRepo,
        AccountRepository $companyRepo
    ) {
        parent::__construct();

        $this->faker = Factory::create();

        $this->clientRepo = $clientRepo;
        $this->invoiceRepo = $invoiceRepo;
        $this->paymentRepo = $paymentRepo;
        $this->vendorRepo = $vendorRepo;
        $this->expenseRepo = $expenseRepo;
        $this->taskRepo = $taskRepo;
        $this->projectRepo = $projectRepo;
        $this->accountRepo = $companyRepo;
        $this->ticketRepo = $ticketRepo;
    }

    public function handle(): bool
    {
        if (Utils::isNinjaProd()) {
            $this->info('Unable to run in production');

            return false;
        }

        $this->info(date('r') . ' Running CreateTestData...');
        $this->count = $this->argument('count');

        if ($database = $this->option('database')) {
            config(['database.default' => $database]);
        }

        if (filter_var($this->argument('create_account'), FILTER_VALIDATE_BOOLEAN)) {
            $this->info('Creating new company...');
            $company = $this->accountRepo->create(
                $this->faker->firstName,
                $this->faker->lastName,
                $this->faker->safeEmail
            );
            Auth::login($company->users[0]);
        } else {
            $this->info('Using second company...');
            Auth::loginUsingId(1);
        }

        //$this->createTicketStubs();
        //$this->createTicketTemplates();
        $this->createClients();
        //$this->createVendors();
        $this->createOtherObjects();

        $this->info('Done');
        return 0;
    }

    private function createClients(): void
    {
        for ($i = 0; $i < $this->count; $i++) {
            $data = [
                'name'        => $this->faker->name,
                'address1'    => $this->faker->streetAddress,
                'address2'    => $this->faker->secondaryAddress,
                'city'        => $this->faker->city,
                'state'       => $this->faker->state,
                'postal_code' => $this->faker->postcode,
                'contacts'    => [[
                    'first_name' => $this->faker->firstName,
                    'last_name'  => $this->faker->lastName,
                    'email'      => $this->faker->safeEmail,
                    'phone'      => $this->faker->phoneNumber,
                ]],
            ];

            $client = $this->clientRepo->save($data);
            $this->info('Client: ' . $client->name);

            $this->createInvoices($client);
            $this->createInvoices($client, true);
            $this->createTasks($client);
            $this->createTickets($client);
        }
    }

    private function createInvoices($client, bool $isQuote = false): void
    {
        for ($i = 0; $i < $this->count; $i++) {
            $data = [
                'is_public'        => true,
                'is_quote'         => $isQuote,
                'client_id'        => $client->id,
                'invoice_date_sql' => date_create()->modify(rand(-100, 100) . ' days')->format('Y-m-d'),
                'due_date_sql'     => date_create()->modify(rand(-100, 100) . ' days')->format('Y-m-d'),
                'invoice_items'    => [[
                    'product_key' => $this->faker->word,
                    'qty'         => $this->faker->randomDigit + 1,
                    'cost'        => $this->faker->randomFloat(2, 1, 10),
                    'notes'       => $this->faker->text($this->faker->numberBetween(50, 300)),
                ]],
            ];

            $invoice = $this->invoiceRepo->save($data);
            $this->info('Invoice: ' . $invoice->invoice_number);

            if (! $isQuote) {
                $this->createPayment($client, $invoice);
            }
        }
    }

    private function createPayment($client, $invoice): void
    {
        $data = [
            'invoice_id'       => $invoice->id,
            'client_id'        => $client->id,
            'amount'           => $this->faker->randomFloat(2, 0, $invoice->amount),
            'payment_date_sql' => date_create()->modify(rand(-100, 100) . ' days')->format('Y-m-d'),
        ];

        $payment = $this->paymentRepo->save($data);

        $this->info('Payment: ' . $payment->amount);
    }

    private function createTasks($client): void
    {
        $data = [
            'client_id' => $client->id,
            'name'      => $this->faker->sentence(3),
        ];
        $project = $this->projectRepo->save($data);

        for ($i = 0; $i < $this->count; $i++) {
            $startTime = date_create()->modify(rand(-100, 100) . ' days')->format('U');
            $endTime = $startTime + (60 * 60 * 2);
            $timeLog = "[[{$startTime},{$endTime}]]";
            $data = [
                'client_id'   => $client->id,
                'project_id'  => $project->id,
                'description' => $this->faker->text($this->faker->numberBetween(50, 300)),
                'time_log'    => $timeLog,
            ];

            $this->taskRepo->save(false, $data);
        }
    }

    private function createTickets($client): void
    {
        $this->info('creating tickets');

        for ($i = 0; $i < $this->count; $i++) {
            $maxTicketNumber = Ticket::getNextTicketNumber(Auth::user()->company->id);

            $this->info('next ticket number = ' . $maxTicketNumber);

            $data = [
                'priority_id'   => TICKET_PRIORITY_LOW,
                'category_id'   => 1,
                'client_id'     => $client->id,
                'is_deleted'    => 0,
                'is_internal'   => (bool) random_int(0, 1),
                'status_id'     => random_int(1, 3),
                'category_id'   => 1,
                'subject'       => $this->faker->realText(10),
                'description'   => $this->faker->realText(50),
                'tags'          => json_encode($this->faker->words($nb = 5, $asText = false)),
                'private_notes' => $this->faker->realText(50),
                'ccs'           => json_encode([]),
                'contact_key'   => $client->getPrimaryContact()->contact_key,
                'due_at'        => Carbon::now(),
                'ticket_number' => $maxTicketNumber ? $maxTicketNumber : 1,
                'action'        => TICKET_SAVE_ONLY,
            ];

            $ticket = $this->ticketRepo->save($data);

            $ticketComment = TicketComment::createNew($ticket);
            $ticketComment->description = $this->faker->realText(70);
            $ticketComment->contact_key = $client->getPrimaryContact()->contact_key;
            $ticket->comments()->save($ticketComment);

            $ticketComment = TicketComment::createNew($ticket);
            $ticketComment->description = $this->faker->realText(40);
            $ticketComment->user_id = 1;
            $ticket->comments()->save($ticketComment);

            $this->info("Ticket: - {$ticket->ticket_number} - {$client->company->company_ticket_settings->ticket_number_start} - {$maxTicketNumber}");
        }
    }

    private function createVendors(): void
    {
        for ($i = 0; $i < $this->count; $i++) {
            $data = [
                'name'            => $this->faker->name,
                'address1'        => $this->faker->streetAddress,
                'address2'        => $this->faker->secondaryAddress,
                'city'            => $this->faker->city,
                'state'           => $this->faker->state,
                'postal_code'     => $this->faker->postcode,
                'vendor_contacts' => [[
                    'first_name' => $this->faker->firstName,
                    'last_name'  => $this->faker->lastName,
                    'email'      => $this->faker->safeEmail,
                    'phone'      => $this->faker->phoneNumber,
                ]],
            ];

            $vendor = $this->vendorRepo->save($data);
            $this->info('Vendor: ' . $vendor->name);

            $this->createExpense($vendor);
        }
    }

    private function createExpense($vendor): void
    {
        for ($i = 0; $i < $this->count; $i++) {
            $data = [
                'vendor_id'    => $vendor->id,
                'amount'       => $this->faker->randomFloat(2, 1, 10),
                'expense_date' => date_create()->modify(rand(-100, 100) . ' days')->format('Y-m-d'),
                'public_notes' => '',
            ];

            $expense = $this->expenseRepo->save($data);
            $this->info('Expense: ' . $expense->amount);
        }
    }

    private function createOtherObjects(): void
    {
        $this->createTaxRate('Tax 1', 10, 1);
        $this->createTaxRate('Tax 2', 20, 2);

        $this->createCategory('Category 1', 1);
        $this->createCategory('Category 1', 2);

        $this->createProject('Project 1', 1);
        $this->createProject('Project 2', 2);
    }

    private function createTaxRate(string $name, int $rate, int $publicId): void
    {
        $taxRate = new TaxRate();
        $taxRate->name = $name;
        $taxRate->rate = $rate;
        $taxRate->company_id = 1;
        $taxRate->user_id = 1;
        $taxRate->public_id = $publicId;
        $taxRate->company_id = $taxRate->company_id;
        $taxRate->save();
    }

    private function createCategory(string $name, int $publicId): void
    {
        $category = new ExpenseCategory();
        $category->name = $name;
        $category->company_id = 1;
        $category->user_id = 1;
        $category->public_id = $publicId;
        $category->company_id = $category->company_id;
        $category->save();
    }

    private function createProject(string $name, int $publicId): void
    {
        $project = new Project();
        $project->name = $name;
        $project->company_id = 1;
        $project->client_id = 1;
        $project->user_id = 1;
        $project->public_id = $publicId;
        $project->company_id = $project->company_id;
        $project->save();
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    private function createTicketTemplates(): void
    {
        $ticketTemplate = TicketTemplate::createNew();
        $ticketTemplate->name = 'Default response';
        $ticketTemplate->description = $this->faker->realText(50);
        $ticketTemplate->save();

        $ticketTemplate = TicketTemplate::createNew();
        $ticketTemplate->name = 'Updated ticket';
        $ticketTemplate->description = $this->faker->realText(50);
        $ticketTemplate->save();

        $ticketTemplate = TicketTemplate::createNew();
        $ticketTemplate->name = 'Ticket closed';
        $ticketTemplate->description = $this->faker->realText(50);
        $ticketTemplate->save();

        $ticketTemplate = TicketTemplate::createNew();
        $ticketTemplate->name = 'Generic response';
        $ticketTemplate->description = $this->faker->realText(50);
        $ticketTemplate->save();
    }
}
