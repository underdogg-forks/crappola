<?php

namespace App\Console\Commands;

use App\Models\DbServer;
use App\Models\LookupAccount;
use App\Models\LookupAccountToken;
use App\Models\LookupCompanyPlan;
use App\Models\LookupContact;
use App\Models\LookupInvitation;
use App\Models\LookupUser;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Console\Command;
use Mail;

class InitLookup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ninja:init-lookup {--truncate=} {--subdomain} {--validate=} {--update=} {--company_id=} {--page_size=100} {--database=db-ninja-1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize lookup tables';

    protected $log = '';

    protected $isValid = true;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->logMessage('Running InitLookup...');

        config(['database.default' => DB_NINJA_LOOKUP]);

        $database = $this->option('database');
        $dbServer = DbServer::whereName($database)->first();

        if ($this->option('subdomain')) {
            $this->logMessage('Updating subdomains...');
            $this->popuplateSubdomains();
        } elseif ($this->option('truncate')) {
            $this->logMessage('Truncating data...');
            $this->truncateTables();
        } else {
            config(['database.default' => $this->option('database')]);

            $count = DB::table('companies')
                ->where('id', '>=', $this->option('company_id') ?: 1)
                ->count();

            for ($i = 0; $i < $count; $i += (int)$this->option('page_size')) {
                $this->initCompanies($dbServer->id, $i);
            }
        }

        $this->logMessage('Results: ' . ($this->isValid ? RESULT_SUCCESS : RESULT_FAILURE));

        if ($this->option('validate')) {
            if ($errorEmail = env('ERROR_EMAIL')) {
                Mail::raw($this->log, function ($message) use ($errorEmail, $database): void {
                    $message->to($errorEmail)
                        ->from(CONTACT_EMAIL)
                        ->subject("Check-Lookups [{$database}]: " . strtoupper($this->isValid ? RESULT_SUCCESS : RESULT_FAILURE));
                });
            } elseif (!$this->isValid) {
                throw new Exception('Check lookups failed!!');
            }
        }
        return 0;
    }

    private function logMessage($str): void
    {
        $str = date('Y-m-d h:i:s') . ' ' . $str;
        $this->info($str);
        $this->log .= $str . "\n";
    }

    private function popuplateSubdomains(): void
    {
        $data = [];

        config(['database.default' => $this->option('database')]);

        $companys = DB::table('companies')
            ->orderBy('id')
            ->where('subdomain', '!=', '')
            ->get(['account_key', 'subdomain']);
        foreach ($companys as $company) {
            $data[$company->account_key] = $company->subdomain;
        }

        config(['database.default' => DB_NINJA_LOOKUP]);

        $validate = $this->option('validate');
        $update = $this->option('update');

        foreach ($data as $companyKey => $subdomain) {
            LookupAccount::whereAccountKey($companyKey)->update(['subdomain' => $subdomain]);
        }
    }

    private function truncateTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::statement('truncate lookup_companies');
        DB::statement('truncate lookup_accounts');
        DB::statement('truncate lookup_users');
        DB::statement('truncate lookup_contacts');
        DB::statement('truncate lookup_invitations');
        DB::statement('truncate lookup_proposal_invitations');
        DB::statement('truncate lookup_account_tokens');
        DB::statement('truncate lookup_ticket_invitations');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function initCompanies($dbServerId, $offset = 0): void
    {
        $data = [];

        config(['database.default' => $this->option('database')]);

        $companies = DB::table('companies')
            ->offset($offset)
            ->limit((int)$this->option('page_size'))
            ->orderBy('id')
            ->where('id', '>=', $this->option('company_id') ?: 1)
            ->get(['id']);
        foreach ($companies as $companyPlan) {
            $data[$companyPlan->id] = $this->parseCompanyPlan($companyPlan->id);
        }

        config(['database.default' => DB_NINJA_LOOKUP]);

        $validate = $this->option('validate');
        $update = $this->option('update');

        foreach ($data as $companyId => $companyPlan) {
            $lookupCompanyPlan = false;
            if ($validate || $update) {
                $lookupCompanyPlan = LookupCompanyPlan::whereDbServerId($dbServerId)->whereCompanyPlanId($companyId)->first();
            }
            if ($validate && !$lookupCompanyPlan) {
                $this->logError("LookupCompanyPlan - dbServerId: {$dbServerId}, companyId: {$companyId} | Not found!");
                continue;
            }
            if (!$lookupCompanyPlan) {
                $lookupCompanyPlan = LookupCompanyPlan::create([
                    'db_server_id' => $dbServerId,
                    'company_id' => $companyId,
                ]);
            }

            foreach ($companyPlan as $companyKey => $company) {
                $lookupAccount = false;
                if ($validate || $update) {
                    $lookupAccount = LookupAccount::whereLookupCompanyPlanId($lookupCompanyPlan->id)->whereAccountKey($companyKey)->first();
                }
                if ($validate && !$lookupAccount) {
                    $this->logError("LookupAccount - lookupCompanyPlanId: {$lookupCompanyPlan->id}, accountKey {$companyKey} | Not found!");
                    continue;
                }
                if (!$lookupAccount) {
                    $lookupAccount = LookupAccount::create([
                        'lookup_company_id' => $lookupCompanyPlan->id,
                        'account_key' => $companyKey,
                    ]);
                }

                foreach ($company['users'] as $user) {
                    $lookupUser = false;
                    if ($validate || $update) {
                        $lookupUser = LookupUser::whereLookupAccountId($lookupAccount->id)->whereUserId($user['user_id'])->first();
                    }
                    if ($validate) {
                        if (!$lookupUser) {
                            $this->logError("LookupUser - lookupAccountId: {$lookupAccount->id}, userId: {$user['user_id']} | Not found!");
                            continue;
                        } elseif ($user['email'] != $lookupUser->email || $user['oauth_user_key'] != $lookupUser->oauth_user_key || $user['referral_code'] != $lookupUser->referral_code) {
                            $this->logError("LookupUser - lookupAccountId: {$lookupAccount->id}, userId: {$user['user_id']} | Out of date!");
                            continue;
                        }
                    }
                    if ($update && $lookupUser) {
                        if ($user['email'] != $lookupUser->email || $user['oauth_user_key'] != $lookupUser->oauth_user_key || $user['referral_code'] != $lookupUser->referral_code) {
                            $lookupUser->email = $user['email'];
                            $lookupUser->oauth_user_key = $user['oauth_user_key'];
                            $lookupUser->referral_code = $user['referral_code'];
                            $lookupUser->save();
                        }
                    } elseif (!$lookupUser) {
                        LookupUser::create([
                            'lookup_account_id' => $lookupAccount->id,
                            'email' => $user['email'] ?: null,
                            'user_id' => $user['user_id'],
                            'oauth_user_key' => $user['oauth_user_key'],
                            'referral_code' => $user['referral_code'],
                        ]);
                    }
                }

                foreach ($company['contacts'] as $contact) {
                    $lookupContact = false;
                    if ($validate || $update) {
                        $lookupContact = LookupContact::whereLookupAccountId($lookupAccount->id)->whereContactKey($contact['contact_key'])->first();
                    }
                    if ($validate && !$lookupContact) {
                        $this->logError("LookupContact - lookupAccountId: {$lookupAccount->id}, contactKey: {$contact['contact_key']} | Not found!");
                        continue;
                    }
                    if (!$lookupContact) {
                        LookupContact::create([
                            'lookup_account_id' => $lookupAccount->id,
                            'contact_key' => $contact['contact_key'],
                        ]);
                    }
                }

                foreach ($company['invitations'] as $invitation) {
                    $lookupInvitation = false;
                    if ($validate || $update) {
                        $lookupInvitation = LookupInvitation::whereLookupAccountId($lookupAccount->id)->whereInvitationKey($invitation['invitation_key'])->first();
                    }
                    if ($validate) {
                        if (!$lookupInvitation) {
                            $this->logError("LookupInvitation - lookupAccountId: {$lookupAccount->id}, invitationKey: {$invitation['invitation_key']} | Not found!");
                            continue;
                        } elseif ($invitation['message_id'] && $lookupInvitation->message_id != $invitation['message_id']) {
                            $this->logError("LookupInvitation - lookupAccountId: {$lookupAccount->id}, invitationKey: {$invitation['invitation_key']} | Not the same!");
                            continue;
                        }
                    }
                    if ($update && $lookupInvitation) {
                        if ($invitation['message_id'] && $lookupInvitation->message_id != $invitation['message_id']) {
                            $lookupInvitation->message_id = $invitation['message_id'];
                            $lookupInvitation->save();
                        }
                    } elseif (!$lookupInvitation) {
                        LookupInvitation::create([
                            'lookup_account_id' => $lookupAccount->id,
                            'invitation_key' => $invitation['invitation_key'],
                            'message_id' => $invitation['message_id'] ?: null,
                        ]);
                    }
                }

                foreach ($company['tokens'] as $token) {
                    $lookupToken = false;
                    if ($validate || $update) {
                        $lookupToken = LookupAccountToken::whereLookupAccountId($lookupAccount->id)->whereToken($token['token'])->first();
                    }
                    if ($validate && !$lookupToken) {
                        $this->logError("LookupAccountToken - lookupAccountId: {$lookupAccount->id}, token: {$token['token']} | Not found!");
                        continue;
                    }
                    if (!$lookupToken) {
                        LookupAccountToken::create([
                            'lookup_account_id' => $lookupAccount->id,
                            'token' => $token['token'],
                        ]);
                    }
                }
            }
        }
    }

    private function parseCompanyPlan($companyId)
    {
        $data = [];

        config(['database.default' => $this->option('database')]);

        $companys = DB::table('companies')->whereCompanyPlanId($companyId)->orderBy('id')->get([
            'id', 'account_key',
        ]);
        foreach ($companys as $company) {
            $data[$company->account_key] = $this->parseAccount($company->id);
        }

        return $data;
    }

    private function parseAccount($companyId)
    {
        $data = [
            'users' => [],
            'contacts' => [],
            'invitations' => [],
            'tokens' => [],
        ];

        $users = DB::table('users')->whereCompanyPlanId($companyId)->orderBy('id')->get([
            'email',
            'id',
            'oauth_user_id',
            'oauth_provider_id',
            'referral_code',
        ]);
        foreach ($users as $user) {
            $data['users'][] = [
                'email' => $user->email,
                'user_id' => $user->id,
                'oauth_user_key' => ($user->oauth_provider_id && $user->oauth_user_id) ? ($user->oauth_provider_id . '-' . $user->oauth_user_id) : null,
                'referral_code' => $user->referral_code,
            ];
        }

        $contacts = DB::table('contacts')->whereCompanyPlanId($companyId)->orderBy('id')->get([
            'contact_key',
        ]);
        foreach ($contacts as $contact) {
            $data['contacts'][] = [
                'contact_key' => $contact->contact_key,
            ];
        }

        $invitations = DB::table('invitations')->whereCompanyPlanId($companyId)->orderBy('id')->get([
            'invitation_key',
            'message_id',
        ]);
        foreach ($invitations as $invitation) {
            $data['invitations'][] = [
                'invitation_key' => $invitation->invitation_key,
                'message_id' => $invitation->message_id,
            ];
        }

        $tokens = DB::table('account_tokens')->whereCompanyPlanId($companyId)->orderBy('id')->get([
            'token',
        ]);
        foreach ($tokens as $token) {
            $data['tokens'][] = [
                'token' => $token->token,
            ];
        }

        return $data;
    }

    private function logError($str): void
    {
        $this->isValid = false;
        $this->logMessage($str);
    }

    protected function getOptions()
    {
        return [
            ['subdomain', null, InputOption::VALUE_OPTIONAL, 'Subdomain', null],
            ['truncate', null, InputOption::VALUE_OPTIONAL, 'Truncate', null],
            ['company_id', null, InputOption::VALUE_OPTIONAL, 'CompanyPlan Id', null],
            ['page_size', null, InputOption::VALUE_OPTIONAL, 'Page Size', null],
            ['database', null, InputOption::VALUE_OPTIONAL, 'Database', null],
            ['validate', null, InputOption::VALUE_OPTIONAL, 'Validate', null],
        ];
    }
}
