<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddInvoiceTypeSupport extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('invoices', 'is_quote')) {
            DB::update('update invoices set is_quote = is_quote + 1');

            Schema::table('invoices', function ($table) {
                $table->renameColumn('is_quote', 'invoice_type_id');
            });
        }

        Schema::table('accounts', function ($table) {
            $table->boolean('enable_second_tax_rate')->default(false);
        });
    }

    public function down()
    {
        if (Schema::hasColumn('invoices', 'invoice_type_id')) {
            DB::update('update invoices set invoice_type_id = invoice_type_id - 1');
        }

        Schema::table('accounts', function ($table) {
            $table->dropColumn('enable_second_tax_rate');
        });
    }
}
