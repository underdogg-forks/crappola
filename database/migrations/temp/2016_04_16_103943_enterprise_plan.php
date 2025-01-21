<?php

use App\Libraries\Utils;
use App\Models\Company;
use App\Models\CompanyPlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnterprisePlan extends Migration
{
    public function up(): void
    {
        $timeout = ini_get('max_execution_time');
        if ($timeout == 0) {
            $timeout = 600;
        }
        $timeout = max($timeout - 10, $timeout * .9);
        $startTime = time();

        if (! Schema::hasTable('company_plans')) {
            Schema::create('company_plans', function ($table): void {
                $table->increments('id');

                $table->enum('plan', ['pro', 'company_plans', 'white_label'])->nullable();
                $table->enum('plan_term', ['month', 'year'])->nullable();
                $table->date('plan_started')->nullable();
                $table->date('plan_paid')->nullable();
                $table->date('plan_expires')->nullable();

                $table->unsignedInteger('payment_id')->nullable();

                $table->date('trial_started')->nullable();
                $table->enum('trial_plan', ['pro', 'company_plans'])->nullable();

                $table->enum('pending_plan', ['pro', 'company_plans', 'free'])->nullable();
                $table->enum('pending_term', ['month', 'year'])->nullable();

                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table('company_plans', function ($table): void {
                $table->foreign('payment_id')->references('id')->on('payments');
            });
        }

        if (! Schema::hasColumn('company_plans', 'company_id')) {
            Schema::table('company_plans', function ($table): void {
                $table->unsignedInteger('company_id')->nullable();
            });
            Schema::table('company_plans', function ($table): void {
                $table->foreign('company_id')->references('id')->on('company_plans')->onDelete('cascade');
            });
        }

        $single_account_ids = DB::table('users')
            ->leftJoin('user_accounts', function ($join): void {
                $join->on('user_accounts.user_id1', '=', 'users.id');
                $join->orOn('user_accounts.user_id2', '=', 'users.id');
                $join->orOn('user_accounts.user_id3', '=', 'users.id');
                $join->orOn('user_accounts.user_id4', '=', 'users.id');
                $join->orOn('user_accounts.user_id5', '=', 'users.id');
            })
            ->leftJoin('company_plans', 'company_plans.id', '=', 'users.company_id')
            ->whereNull('user_accounts.id')
            ->whereNull('company_plans.company_id')
            ->where(function ($query): void {
                $query->whereNull('users.public_id');
                $query->orWhere('users.public_id', '=', 0);
            })
            ->pluck('users.company_id');

        if (count($single_account_ids)) {
            foreach (Company::find($single_account_ids) as $company) {
                $this->upAccounts($company);
                $this->checkTimeout($timeout, $startTime);
            }
        }

        $group_accounts = DB::select(
            'SELECT u1.company_id as account1, u2.company_id as account2, u3.company_id as account3, u4.company_id as account4, u5.company_id as account5 FROM `user_accounts`
            LEFT JOIN users u1 ON (u1.public_id IS NULL OR u1.public_id = 0) AND user_accounts.user_id1 = u1.id
            LEFT JOIN users u2 ON (u2.public_id IS NULL OR u2.public_id = 0) AND user_accounts.user_id2 = u2.id
            LEFT JOIN users u3 ON (u3.public_id IS NULL OR u3.public_id = 0) AND user_accounts.user_id3 = u3.id
            LEFT JOIN users u4 ON (u4.public_id IS NULL OR u4.public_id = 0) AND user_accounts.user_id4 = u4.id
            LEFT JOIN users u5 ON (u5.public_id IS NULL OR u5.public_id = 0) AND user_accounts.user_id5 = u5.id
            LEFT JOIN companies a1 ON a1.id = u1.company_id
            LEFT JOIN companies a2 ON a2.id = u2.company_id
            LEFT JOIN companies a3 ON a3.id = u3.company_id
            LEFT JOIN companies a4 ON a4.id = u4.company_id
            LEFT JOIN companies a5 ON a5.id = u5.company_id
            WHERE (a1.id IS NOT NULL AND a1.company_id IS NULL)
            OR (a2.id IS NOT NULL AND a2.company_id IS NULL)
            OR (a3.id IS NOT NULL AND a3.company_id IS NULL)
            OR (a4.id IS NOT NULL AND a4.company_id IS NULL)
            OR (a5.id IS NOT NULL AND a5.company_id IS NULL)'
        );

        if (count($group_accounts)) {
            foreach ($group_accounts as $group_account) {
                $this->upAccounts(null, Company::find(get_object_vars($group_account)));
                $this->checkTimeout($timeout, $startTime);
            }
        }
    }

    private function upAccounts($primaryAccount, $otherAccounts = []): void
    {
        if (! $primaryAccount) {
            $primaryAccount = $otherAccounts->first();
        }

        if (empty($primaryAccount)) {
            return;
        }

        $companyPlan = CompanyPlan::create();
        if ($primaryAccount->pro_plan_paid && $primaryAccount->pro_plan_paid != '0000-00-00') {
            $companyPlan->plan = 'pro';
            $companyPlan->plan_term = 'year';
            $companyPlan->plan_started = $primaryAccount->pro_plan_paid;
            $companyPlan->plan_paid = $primaryAccount->pro_plan_paid;

            $expires = DateTime::createFromFormat('Y-m-d', $primaryAccount->pro_plan_paid);
            $expires->modify('+1 year');
            $expires = $expires->format('Y-m-d');

            // check for self-host white label licenses
            if (! Utils::isNinjaProd()) {
                if ($companyPlan->plan_paid) {
                    $companyPlan->plan = 'white_label';
                    // old ones were unlimited, new ones are yearly
                    if ($companyPlan->plan_paid == NINJA_DATE) {
                        $companyPlan->plan_term = null;
                    } else {
                        $companyPlan->plan_term = PLAN_TERM_YEARLY;
                        $companyPlan->plan_expires = $expires;
                    }
                }
            } elseif ($companyPlan->plan_paid != NINJA_DATE) {
                $companyPlan->plan_expires = $expires;
            }
        }

        if ($primaryAccount->pro_plan_trial && $primaryAccount->pro_plan_trial != '0000-00-00') {
            $companyPlan->trial_started = $primaryAccount->pro_plan_trial;
            $companyPlan->trial_plan = 'pro';
        }

        $companyPlan->save();

        $primaryAccount->company_id = $companyPlan->id;
        $primaryAccount->save();

        if (! empty($otherAccounts)) {
            foreach ($otherAccounts as $company) {
                if ($company && $company->id != $primaryAccount->id) {
                    $company->company_id = $companyPlan->id;
                    $company->save();
                }
            }
        }
    }

    protected function checkTimeout($timeout, $startTime): void
    {
        if (time() - $startTime >= $timeout) {
            exit('Migration reached time limit; please run again to continue');
        }
    }

    public function down(): void
    {
        $timeout = ini_get('max_execution_time');
        if ($timeout == 0) {
            $timeout = 600;
        }
        $timeout = max($timeout - 10, $timeout * .9);
        $startTime = time();

        if (! Schema::hasColumn('company_plans', 'pro_plan_paid')) {
            Schema::table('company_plans', function ($table): void {
                $table->date('pro_plan_paid')->nullable();
                $table->date('pro_plan_trial')->nullable();
            });
        }

        $company_ids = DB::table('company_plans')
            ->leftJoin('company_plans', 'company_plans.company_id', '=', 'company_plans.id')
            ->whereNull('company_plans.pro_plan_paid')
            ->whereNull('company_plans.pro_plan_trial')
            ->where(function ($query): void {
                $query->whereNotNull('company_plans.plan_paid');
                $query->orWhereNotNull('company_plans.trial_started');
            })
            ->pluck('company_plans.id');

        $company_ids = array_unique($company_ids);

        if (count($company_ids)) {
            foreach (CompanyPlan::find($company_ids) as $companyPlan) {
                foreach ($companyPlan->accounts as $company) {
                    $company->pro_plan_paid = $companyPlan->plan_paid;
                    $company->pro_plan_trial = $companyPlan->trial_started;
                    $company->save();
                }
                $this->checkTimeout($timeout, $startTime);
            }
        }

        Schema::dropIfExists('company_plans');
    }
}
