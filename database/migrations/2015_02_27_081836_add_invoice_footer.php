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
        Schema::table('accounts', function ($table): void {
            $table->text('invoice_footer')->nullable();
        });

        Schema::table('invoices', function ($table): void {
            $table->text('invoice_footer')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('accounts', function ($table): void {
            $table->dropColumn('invoice_footer');
        });

        Schema::table('invoices', function ($table): void {
            $table->dropColumn('invoice_footer');
        });
    }
};
