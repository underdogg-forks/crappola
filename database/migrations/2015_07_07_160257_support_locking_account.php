<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function ($table): void {
            $table->smallInteger('failed_logins')->nullable();
        });

        Schema::table('account_gateways', function ($table): void {
            $table->boolean('show_address')->default(true)->nullable();
            $table->boolean('update_address')->default(true)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function ($table): void {
            $table->dropColumn('failed_logins');
        });

        Schema::table('account_gateways', function ($table): void {
            $table->dropColumn('show_address');
            $table->dropColumn('update_address');
        });
    }
};
