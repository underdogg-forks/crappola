<?php

use Illuminate\Database\Migrations\Migration;

class AddClientPassword extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->boolean('enable_portal_password')->default(0);
            $table->boolean('send_portal_password')->default(0);
        });

        Schema::table('contacts', function ($table): void {
            $table->string('password', 255)->nullable();
            $table->boolean('confirmation_code', 255)->nullable();
            $table->boolean('remember_token', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('companies', function ($table): void {
            $table->dropColumn('enable_portal_password');
            $table->dropColumn('send_portal_password');
        });

        Schema::table('contacts', function ($table): void {
            $table->dropColumn('password');
            $table->dropColumn('confirmation_code');
            $table->dropColumn('remember_token');
        });
    }
}
