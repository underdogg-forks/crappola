<?php

namespace App\Console\Commands;

use App\Jobs\ExportReportResults;
use App\Jobs\RunReport;
use App\Jobs\SendInvoiceEmail;
use App\Libraries\CurlUtils;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\ScheduledReport;
use App\Ninja\Mailers\UserMailer;
use App\Ninja\Repositories\AccountRepository;
use App\Ninja\Repositories\InvoiceRepository;
use App\Services\PaymentService;
use Auth;
use DateTime;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Console\Command;
use Mail;
use Symfony\Component\Console\Input\InputOption;
use App\Libraries\Utils;

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
     * @var InvoiceRepository
     */
    protected $invoiceRepo;

    /**
     * @var accountRepository
     */
    protected $companyRepo;

    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * SendReminders constructor.
     *
     * @param Mailer $mailer
     * @param InvoiceRepository $invoiceRepo
     * @param accountRepository $companyRepo
     */
    public function __construct(InvoiceRepository $invoiceRepo, PaymentService $paymentService, AccountRepository $companyRepo, UserMailer $userMailer)
    {
        parent::__construct();

        $this->paymentService = $paymentService;
        $this->invoiceRepo = $invoiceRepo;
        $this->accountRepo = $companyRepo;
        $this->userMailer = $userMailer;
    }

    public function handle(): void
    {
        $this->info(date('r') . ' Running SendReminders...');

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
            Mail::raw('EOM', function ($message) use ($errorEmail, $database): void {
                $message->to($errorEmail)
                    ->from(CONTACT_EMAIL)
                    ->subject("SendReminders [{$database}]: Finished successfully");
            });
        }
    }

    private function billInvoices(): void
    {
        $today = new DateTime();

        $delayedAutoBillInvoices = Invoice::with('company.timezone', 'recurring_invoice', 'invoice_items', 'client', 'user')
            ->whereRaw(
                'is_deleted IS FALSE AND deleted_at IS NULL AND is_recurring IS FALSE AND is_public IS TRUE
            AND balance > 0 AND due_date = ? AND recurring_invoice_id IS NOT NULL',
                [$today->format('Y-m-d')]
            )
            ->orderBy('invoices.id', 'asc')
            ->get();
        $this->info(date('r ') . $delayedAutoBillInvoices->count() . ' due recurring invoice instance(s) found');

        /** @var Invoice $invoice */
        foreach ($delayedAutoBillInvoices as $invoice) {
            if ($invoice->isPaid()) {
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

    private function chargeLateFees(): void
    {
        $companys = $this->accountRepo->findWithFees();
        $this->info(date('r ') . $companys->count() . ' accounts found with fees');

        foreach ($companys as $company) {
            if (!$company->hasFeature(FEATURE_EMAIL_TEMPLATES_REMINDERS)) {
                continue;
            }

            $invoices = $this->invoiceRepo->findNeedingReminding($company, false);
            $this->info(date('r ') . $company->name . ': ' . $invoices->count() . ' invoices found');

            foreach ($invoices as $invoice) {
                if ($reminder = $company->getInvoiceReminder($invoice, false)) {
                    $this->info(date('r') . ' Charge fee: ' . $invoice->id);
                    $company->loadLocalizationSettings($invoice->client); // support trans to add fee line item
                    $number = preg_replace('/[^0-9]/', '', $reminder);

                    if ($invoice->isQuote()) {
                        $amount = $company->account_email_settings->{"late_fee_quote{$number}_amount"};
                        $percent = $company->account_email_settings->{"late_fee_quote{$number}_percent"};
                    } else {
                        $amount = $company->account_email_settings->{"late_fee{$number}_amount"};
                        $percent = $company->account_email_settings->{"late_fee{$number}_percent"};
                    }

                    $this->invoiceRepo->setLateFee($invoice, $amount, $percent);
                }
            }
        }
    }

    private function sendReminderEmails(): void
    {
        $companys = $this->accountRepo->findWithReminders();
        $this->info(date('r ') . count($companys) . ' accounts found with reminders');

        foreach ($companys as $company) {
            if (!$company->hasFeature(FEATURE_EMAIL_TEMPLATES_REMINDERS)) {
                continue;
            }

            // standard reminders
            $invoices = $this->invoiceRepo->findNeedingReminding($company);
            $this->info(date('r ') . $company->name . ': ' . $invoices->count() . ' invoices found');

            foreach ($invoices as $invoice) {
                if ($reminder = $company->getInvoiceReminder($invoice)) {
                    if ($invoice->last_sent_date == date('Y-m-d')) {
                        continue;
                    }
                    $this->info(date('r') . ' Send email: ' . $invoice->id);
                    dispatch(new SendInvoiceEmail($invoice, $invoice->user_id, $reminder));
                }
            }

            // endless reminders
            $invoices = $this->invoiceRepo->findNeedingEndlessReminding($company);
            $this->info(date('r ') . $company->name . ': ' . $invoices->count() . ' endless invoices found');

            foreach ($invoices as $invoice) {
                if ($invoice->last_sent_date == date('Y-m-d')) {
                    continue;
                }
                $this->info(date('r') . ' Send email: ' . $invoice->id);
                dispatch(new SendInvoiceEmail($invoice, $invoice->user_id, 'reminder4'));
            }

            // endless quote reminders
            $invoices = $this->invoiceRepo->findNeedingEndlessReminding($company, true);
            $this->info(date('r ') . $company->name . ': ' . $invoices->count() . ' endless quotes found');

            foreach ($invoices as $invoice) {
                if ($invoice->last_sent_date == date('Y-m-d')) {
                    continue;
                }
                $this->info(date('r') . ' Send email: ' . $invoice->id);
                dispatch(new SendInvoiceEmail($invoice, $invoice->user_id, 'quote_reminder4'));
            }
        }
    }

    private function sendScheduledReports(): void
    {
        $scheduledReports = ScheduledReport::where('send_date', '<=', date('Y-m-d'))
            ->with('user', 'company.companyPlan')
            ->get();
        $this->info(date('r ') . $scheduledReports->count() . ' scheduled reports');

        foreach ($scheduledReports as $scheduledReport) {
            $this->info(date('r') . ' Processing report: ' . $scheduledReport->id);

            $user = $scheduledReport->user;
            $company = $scheduledReport->company;
            $company->loadLocalizationSettings();

            if (!$company->hasFeature(FEATURE_REPORTS)) {
                continue;
            }

            $config = (array)json_decode($scheduledReport->config);
            $reportType = $config['report_type'];

            // send email as user
            auth()->onceUsingId($user->id);

            $report = dispatch_now(new RunReport($scheduledReport->user, $reportType, $config, $company, true));
            $file = dispatch_now(new ExportReportResults($scheduledReport->user, $config['export_format'], $reportType, $report->exportParams));

            if ($file) {
                try {
                    $this->userMailer->sendScheduledReport($scheduledReport, $file);
                    $this->info(date('r') . ' Sent report');
                } catch (Exception $exception) {
                    $this->info(date('r') . ' ERROR: ' . $exception->getMessage());
                }
            } else {
                $this->info(date('r') . ' ERROR: Failed to run report');
            }

            $scheduledReport->updateSendDate();

            auth()->logout();
        }
    }

    private function loadExchangeRates(): void
    {
        if (Utils::isNinjaDev()) {
            return;
        }

        if (config('ninja.exchange_rates_enabled')) {
            $this->info(date('r') . ' Loading latest exchange rates...');

            $url = config('ninja.exchange_rates_url');
            $apiKey = config('ninja.exchange_rates_api_key');
            $url = str_replace('{apiKey}', $apiKey, $url);

            $response = CurlUtils::get($url);
            $data = json_decode($response);

            if ($data && property_exists($data, 'rates') && property_exists($data, 'base')) {
                $base = config('ninja.exchange_rates_base');

                // should calculate to different base
                $recalculate = ($data->base != $base);

                foreach ($data->rates as $code => $rate) {
                    if ($recalculate) {
                        $rate = 1 / $data->rates->{$base} * $rate;
                    }

                    Currency::whereCode($code)->update(['exchange_rate' => $rate]);
                }
            } else {
                $this->info(date('r') . ' Error: failed to load exchange rates - ' . $response);
                DB::table('currencies')->update(['exchange_rate' => 1]);
            }
        } else {
            DB::table('currencies')->update(['exchange_rate' => 1]);
        }

        CurlUtils::get(SITE_URL . '?clear_cache=true');
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
