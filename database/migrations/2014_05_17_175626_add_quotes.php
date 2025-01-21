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
        Schema::table('invoices', function ($table): void {
            $table->boolean('invoice_type_id')->default(0);
            $table->unsignedInteger('quote_id')->nullable();
            $table->unsignedInteger('quote_invoice_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invoices', function ($table): void {
            $table->dropColumn('invoice_type_id');
            $table->dropColumn('quote_id');
            $table->dropColumn('quote_invoice_id');
        });
    }
};
