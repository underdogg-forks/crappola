<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Ninja\Mailers\ContactMailer as Mailer;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Repositories\InvoiceRepository;
use Illuminate\Console\Command;

/**
 * Class SendReminders.
 */
class SendReminders extends Command
{
    /**
     * @var string
     */
    protected $name = 'ninja:send-reminders';

    /**
     * @var string
     */
    protected $description = 'Send reminder emails';

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepo;

    /**
     * @var accountRepository
     */
    protected $accountRepo;

    /**
     * SendReminders constructor.
     *
     * @param Mailer            $mailer
     * @param InvoiceRepository $invoiceRepo
     * @param accountRepository $accountRepo
     */
    public function __construct(Mailer $mailer, InvoiceRepository $invoiceRepo, AccountRepository $accountRepo)
    {
        parent::__construct();

        $this->mailer = $mailer;
        $this->invoiceRepo = $invoiceRepo;
        $this->accountRepo = $accountRepo;
    }

    public function fire()
    {
        $this->info(date('Y-m-d') . ' Running SendReminders...');

        if ($database = $this->option('database')) {
            config(['database.default' => $database]);
        }

        $this->billInvoices();
        $this->chargeLateFees();
        $this->sendReminderEmails();
        $this->sendScheduledReports();
        $this->loadExchangeRates();

        $this->info(date('r') . ' Done');

        if ($errorEmail = env('ERROR_EMAIL')) {
            \Mail::raw('EOM', function ($message) use ($errorEmail, $database) {
                $message->to($errorEmail)
                        ->from(CONTACT_EMAIL)
                        ->subject("SendReminders [{$database}]: Finished successfully");
            });
        }
        return 0;
    }

    private function billInvoices()
    {
        $today = new DateTime();

        $delayedAutoBillInvoices = Invoice::with('account.timezone', 'recurring_invoice', 'invoice_items', 'client', 'user')
            ->whereRaw('is_deleted IS FALSE AND deleted_at IS NULL AND is_recurring IS FALSE AND is_public IS TRUE
            AND balance > 0 AND due_date = ? AND recurring_invoice_id IS NOT NULL',
                [$today->format('Y-m-d')])
            ->orderBy('invoices.id', 'asc')
            ->get();
        $this->info(date('r ') . $delayedAutoBillInvoices->count() . ' due recurring invoice instance(s) found');

        /** @var Invoice $invoice */
        foreach ($delayedAutoBillInvoices as $invoice) {
            //21-03-2023 adjustment here
            if ($invoice->isPaid() || !$invoice->account || $invoice->account->is_deleted) {
            // if ($invoice->isPaid() || $invoice->account->is_deleted) {
                continue;
            }

            if ($invoice->getAutoBillEnabled() && $invoice->client->autoBillLater()) {
                $this->info(date('r') . ' Processing Autobill-delayed Invoice: ' . $invoice->id);
                Auth::loginUsingId($invoice->activeUser()->id);
                $this->paymentService->autoBillInvoice($invoice);
                Auth::logout();
            }
        }
    }

    private function chargeLateFees()
    {
        $accounts = $this->accountRepo->findWithFees();
        $this->info(date('r ') . $accounts->count() . ' accounts found with fees enabled');

        foreach ($accounts as $account) {
            if (! $account->hasFeature(FEATURE_EMAIL_TEMPLATES_REMINDERS) || $account->account_email_settings->is_disabled) {
                continue;
            }

            $invoices = $this->invoiceRepo->findNeedingReminding($account, false);
            $this->info(date('r ') . $account->name . ': ' . $invoices->count() . ' invoices found');

            foreach ($invoices as $invoice) {
                if ($reminder = $account->getInvoiceReminder($invoice, false)) {
                    $this->info(date('r') . ' Charge fee: ' . $invoice->id);
                    $account->loadLocalizationSettings($invoice->client); // support trans to add fee line item
                    $number = preg_replace('/[^0-9]/', '', $reminder);

                    $amount = $account->account_email_settings->{"late_fee{$number}_amount"};
                    $percent = $account->account_email_settings->{"late_fee{$number}_percent"};
                    $this->invoiceRepo->setLateFee($invoice, $amount, $percent);
                }
            }
        }
    }

    private function sendReminderEmails()
    {
        $accounts = $this->accountRepo->findWithReminders();
        $this->info(count($accounts) . ' accounts found');

        /** @var \App\Models\Account $account */
        foreach ($accounts as $account) {
            if (! $account->hasFeature(FEATURE_EMAIL_TEMPLATES_REMINDERS)) {
                continue;
            }

            $invoices = $this->invoiceRepo->findNeedingReminding($account);
            $this->info($account->name . ': ' . count($invoices) . ' invoices found');

            /** @var Invoice $invoice */
            foreach ($invoices as $invoice) {
                if ($reminder = $account->getInvoiceReminder($invoice)) {
                    $this->info('Send to ' . $invoice->id);
                    $this->mailer->sendInvoice($invoice, $reminder);
                }
            }
        }

        $this->info('Done');

        if ($errorEmail = env('ERROR_EMAIL')) {
            \Mail::raw('EOM', function ($message) use ($errorEmail) {
                $message->to($errorEmail)
                        ->from(CONTACT_EMAIL)
                        ->subject('SendReminders: Finished successfully');
            });
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
        return [];
    }
}
