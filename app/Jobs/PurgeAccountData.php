<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\LookupAccount;
use App\Ninja\Mailers\UserMailer;
use Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PurgeAccountData extends Job
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UserMailer $userMailer): void
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$user->is_admin) {
            throw new Exception(trans('texts.forbidden'));
        }

        // delete the documents from cloud storage
        Document::scope()->each(function ($item, $key): void {
            $item->delete();
        });

        $tables = [
            'activities',
            'invitations',
            'account_gateway_tokens',
            'payment_methods',
            'credits',
            'expense_categories',
            'expenses',
            'recurring_expenses',
            'invoice_items',
            'payments',
            'invoices',
            'tasks',
            'projects',
            'products',
            'vendor_contacts',
            'vendors',
            'contacts',
            'clients',
            'proposals',
            'proposal_templates',
            'proposal_snippets',
            'proposal_categories',
            'proposal_invitations',
            'tax_rates',
        ];

        foreach ($tables as $table) {
            DB::table($table)->where('company_id', '=', $user->company_id)->delete();
        }

        $company->invoice_number_counter = 1;
        $company->quote_number_counter = 1;
        $company->credit_number_counter = $company->credit_number_counter > 0 ? 1 : 0;
        $company->client_number_counter = $company->client_number_counter > 0 ? 1 : 0;
        $company->save();

        session([RECENTLY_VIEWED => false]);

        if (env('MULTI_DB_ENABLED')) {
            $current = config('database.default');
            config(['database.default' => DB_NINJA_LOOKUP]);

            $lookupAccount = LookupAccount::whereAccountKey($company->account_key)->firstOrFail();
            DB::table('lookup_contacts')->where('lookup_account_id', '=', $lookupAccount->id)->delete();
            DB::table('lookup_invitations')->where('lookup_account_id', '=', $lookupAccount->id)->delete();
            DB::table('lookup_proposal_invitations')->where('lookup_account_id', '=', $lookupAccount->id)->delete();

            config(['database.default' => $current]);
        }

        $subject = trans('texts.purge_successful');
        $message = trans('texts.purge_details', ['company' => $user->company->getDisplayName()]);
        $userMailer->sendMessage($user, $subject, $message);
    }
}
