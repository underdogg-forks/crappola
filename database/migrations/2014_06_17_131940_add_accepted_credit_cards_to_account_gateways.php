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
        Schema::table('account_gateways', function ($table): void {
            $table->unsignedInteger('accepted_credit_cards')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('account_gateways', function ($table): void {
            $table->dropColumn('accepted_credit_cards');
        });
    }
};
