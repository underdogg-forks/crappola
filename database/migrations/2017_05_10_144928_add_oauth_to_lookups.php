<?php

use Illuminate\Database\Migrations\Migration;

class AddOauthToLookups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('lookup_users', function ($table): void {
            $table->string('oauth_user_key')->nullable()->unique();
            $table->string('referral_code')->nullable()->unique();
        });

        Schema::table('companies', function ($table): void {
            $table->string('referral_code')->nullable();
        });

        DB::statement('update companies
            left join accounts on accounts.company_id = companies.id
            left join users on users.id = accounts.referral_user_id
            set companies.referral_code = users.referral_code
            where users.id is not null');

        Schema::table('accounts', function ($table): void {
            if (Schema::hasColumn('accounts', 'referral_user_id')) {
                $table->dropColumn('referral_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('lookup_users', function ($table): void {
            $table->dropColumn('oauth_user_key');
            $table->dropColumn('referral_code');
        });

        Schema::table('companies', function ($table): void {
            $table->dropColumn('referral_code');
        });
    }
}
