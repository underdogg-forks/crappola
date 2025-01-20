<?php

use Illuminate\Database\Migrations\Migration;

class AddMultipleDatabaseSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('lookup_companies', function ($table): void {
            $table->unsignedInteger('company_id')->index();
        });

        Schema::table('lookup_companies', function ($table): void {
            $table->unique(['db_server_id', 'company_id']);
        });

        Schema::table('lookup_accounts', function ($table): void {
            $table->string('account_key')->change()->unique();
        });

        Schema::table('lookup_users', function ($table): void {
            $table->string('email')->change()->nullable()->unique();
            $table->string('confirmation_code')->nullable()->unique();
            $table->unsignedInteger('user_id')->index();
        });

        Schema::table('lookup_users', function ($table): void {
            $table->unique(['lookup_account_id', 'user_id']);
        });

        Schema::table('lookup_contacts', function ($table): void {
            $table->string('contact_key')->change()->unique();
        });

        Schema::table('lookup_invitations', function ($table): void {
            $table->string('invitation_key')->change()->unique();
            $table->string('message_id')->change()->nullable()->unique();
        });

        Schema::table('lookup_tokens', function ($table): void {
            $table->string('token')->change()->unique();
        });

        Schema::rename('lookup_tokens', 'lookup_account_tokens');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('lookup_companies', function ($table): void {
            $table->dropColumn('company_id');
        });

        Schema::table('lookup_users', function ($table): void {
            $table->dropColumn('confirmation_code');
        });

        Schema::rename('lookup_account_tokens', 'lookup_tokens');
    }
}
