<?php

use Illuminate\Database\Migrations\Migration;

class AddInvoiceFooter extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->text('invoice_footer')->nullable();
        });

        Schema::table('invoices', function ($table) {
            $table->text('invoice_footer')->nullable();
        });
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
