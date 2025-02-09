<?php

use Illuminate\Database\Migrations\Migration;

class AddCustomInvoiceLabels extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->text('invoice_labels')->nullable();
        });
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('invoice_labels');
        });
    }
}
