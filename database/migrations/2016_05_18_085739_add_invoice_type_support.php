<?php

use Illuminate\Database\Migrations\Migration;

class AddInvoiceTypeSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasColumn('invoices', 'is_quote')) {
            DB::update('update invoices set is_quote = is_quote + 1');

            Schema::table('invoices', function ($table): void {
                $table->renameColumn('is_quote', 'invoice_type_id');
            });
        }

        Schema::table('companies', function ($table): void {
            $table->boolean('enable_second_tax_rate')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'invoice_type_id')) {
            DB::update('update invoices set invoice_type_id = invoice_type_id - 1');
        }

        Schema::table('companies', function ($table): void {
            $table->dropColumn('enable_second_tax_rate');
        });
    }
}
