<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddInvoiceFooter extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});

        Schema::table('invoices', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('invoice_footer');
        });

        Schema::table('invoices', function ($table) {
            $table->dropColumn('invoice_footer');
        });
    }
}
