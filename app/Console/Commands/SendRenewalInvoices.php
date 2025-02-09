<?php

namespace App\Console\Commands;

use App\Libraries\Utils;
use App\Models\Company;
use App\Ninja\Mailers\ContactMailer as Mailer;
use App\Ninja\Repositories\AccountRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class SendRenewalInvoices.
 */
class SendRenewalInvoices extends Command
{
    /**
     * @var string
     */
    protected $name = 'ninja:send-renewals';

    /**
     * @var string
     */
    protected $description = 'Send renewal invoices';

    /**
     * @var Mailer
     */
    protected Mailer $mailer;

    protected AccountRepository $accountRepo;

    /**
     * SendRenewalInvoices constructor.
     *
     * @param Mailer            $mailer
     * @param AccountRepository $repo
     */
    public function __construct(Mailer $mailer, AccountRepository $repo)
    {
        parent::__construct();

        $this->mailer = $mailer;
        $this->accountRepo = $repo;
    }

    public function handle(): void
    {
        $this->info(date('r') . ' Running SendRenewalInvoices...');

        if ($database = $this->option('database')) {
            config(['database.default' => $database]);
        }

        // get all accounts with plans expiring in 10 days
        $companies = Company::whereRaw("datediff(plan_expires, curdate()) = 10 and (plan = 'pro' or plan = 'enterprise')")
            ->orderBy('id')
            ->get();
        $this->info($companies->count() . ' companies found renewing in 10 days');

        foreach ($companies as $company) {
            if ( ! $company->accounts->count()) {
                continue;
            }

            $account = $company->accounts->sortBy('id')->first();
            $plan = [];
            $plan['plan'] = $company->plan;
            $plan['term'] = $company->plan_term;
            $plan['num_users'] = $company->num_users;
            $plan['price'] = min($company->plan_price, Utils::getPlanPrice($plan));
            if ($plan['plan'] == PLAN_FREE) {
                continue;
            }

            if ( ! $plan['plan']) {
                continue;
            }

            if ( ! $plan['term']) {
                continue;
            }

            if ( ! $plan['price']) {
                continue;
            }

            $client = $this->accountRepo->getNinjaClient($account);
            $invitation = $this->accountRepo->createNinjaInvoice($client, $account, $plan, 0);

            // set the due date to 10 days from now
            $invoice = $invitation->invoice;
            $invoice->due_date = date('Y-m-d', strtotime('+ 10 days'));
            $invoice->save();

            $term = $plan['term'];
            $plan = $plan['plan'];

            if ($term == PLAN_TERM_YEARLY) {
                $this->mailer->sendInvoice($invoice);
                $this->info(sprintf('Sent %sly %s invoice to %s', $term, $plan, $client->getDisplayName()));
            } else {
                $this->info(sprintf('Created %sly %s invoice for %s', $term, $plan, $client->getDisplayName()));
            }
        }

        $this->info('Done');

        if ($errorEmail = env('ERROR_EMAIL')) {
            Mail::raw('EOM', function ($message) use ($errorEmail, $database): void {
                $message->to($errorEmail)
                    ->from(CONTACT_EMAIL)
                    ->subject(sprintf('SendRenewalInvoices [%s]: Finished successfully', $database));
            });
        }
    }

    protected function getArguments()
    {
        return [];
    }

    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'Database', null],
        ];
    }
}
