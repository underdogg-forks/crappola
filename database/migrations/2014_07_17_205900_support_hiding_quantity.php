<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class SupportHidingQuantity extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});

        Schema::table('invoices', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('hide_quantity');
            $table->dropColumn('hide_paid_to_date');

            $table->dropColumn('custom_invoice_label1');
            $table->dropColumn('custom_invoice_label2');

            $table->dropColumn('custom_invoice_taxes1');
            $table->dropColumn('custom_invoice_taxes2');
        });

        Schema::table('invoices', function ($table) {
            $table->dropColumn('custom_value1');
            $table->dropColumn('custom_value2');

            $table->dropColumn('custom_taxes1');
            $table->dropColumn('custom_taxes2');
        });
    }
}
