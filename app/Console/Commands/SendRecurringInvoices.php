<?php

namespace App\Console\Commands;

use App\Jobs\SendInvoiceEmail;
use App\Libraries\Utils;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\RecurringExpense;
use App\Ninja\Repositories\InvoiceRepository;
use App\Ninja\Repositories\RecurringExpenseRepository;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class SendRecurringInvoices.
 */
class SendRecurringInvoices extends Command
{
    /**
     * @var string
     */
    protected $name = 'ninja:send-invoices';

    /**
     * @var string
     */
    protected $description = 'Send recurring invoices';

    protected InvoiceRepository $invoiceRepo;

    /**
     * SendRecurringInvoices constructor.
     */
    public function __construct(InvoiceRepository $invoiceRepo, RecurringExpenseRepository $recurringExpenseRepo)
    {
        parent::__construct();

        $this->invoiceRepo = $invoiceRepo;
        $this->recurringExpenseRepo = $recurringExpenseRepo;
    }

    public function handle(): void
    {
        $this->info(date('r') . ' Running SendRecurringInvoices...');

        if ($database = $this->option('database')) {
            config(['database.default' => $database]);
        }

        $this->resetCounters();
        $this->createInvoices();
        $this->createExpenses();

        $this->info(date('r') . ' Done');
    }

    private function resetCounters(): void
    {
        $companys = Company::where('reset_counter_frequency_id', '>', 0)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($companys as $company) {
            $company->checkCounterReset();
        }
    }

    private function createInvoices(): void
    {
        $today = new DateTime();

        $invoices = Invoice::with('company.timezone', 'invoice_items', 'client', 'user')
            ->whereRaw('is_deleted IS FALSE AND deleted_at IS NULL AND is_recurring IS TRUE AND is_public IS TRUE AND frequency_id > 0 AND start_date <= ? AND (end_date IS NULL OR end_date >= ?)', [$today, $today])
            ->orderBy('id', 'asc')
            ->get();
        $this->info(date('r ') . $invoices->count() . ' recurring invoice(s) found');

        foreach ($invoices as $recurInvoice) {
            $shouldSendToday = $recurInvoice->shouldSendToday();

            if (! $shouldSendToday) {
                continue;
            }

            $this->info(date('r') . ' Processing Invoice: ' . $recurInvoice->id);

            $company = $recurInvoice->company;
            $company->loadLocalizationSettings($recurInvoice->client);
            Auth::loginUsingId($recurInvoice->activeUser()->id);

            try {
                $invoice = $this->invoiceRepo->createRecurringInvoice($recurInvoice);
                if ($invoice && ! $invoice->isPaid() && $company->auto_email_invoice) {
                    $this->info(date('r') . ' Not billed - Sending Invoice');
                    dispatch(new SendInvoiceEmail($invoice, $invoice->user_id));
                } elseif ($invoice) {
                    $this->info(date('r') . ' Successfully billed invoice');
                }
            } catch (Exception $exception) {
                $this->info(date('r') . ' Error: ' . $exception->getMessage());
                Utils::logError($exception);
            }

            Auth::logout();
        }
    }

    private function createExpenses(): void
    {
        $today = new DateTime();

        $expenses = RecurringExpense::with('client')
            ->whereRaw('is_deleted IS FALSE AND deleted_at IS NULL AND start_date <= ? AND (end_date IS NULL OR end_date >= ?)', [$today, $today])
            ->orderBy('id', 'asc')
            ->get();
        $this->info(date('r ') . $expenses->count() . ' recurring expenses(s) found');

        foreach ($expenses as $expense) {
            $shouldSendToday = $expense->shouldSendToday();

            if (! $shouldSendToday) {
                continue;
            }

            $this->info(date('r') . ' Processing Expense: ' . $expense->id);
            $this->recurringExpenseRepo->createRecurringExpense($expense);
        }
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
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'Database', null],
        ];
    }
}
