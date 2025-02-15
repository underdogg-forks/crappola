<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddInvoiceNumberPattern extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('invoice_number_pattern');
            $table->dropColumn('quote_number_pattern');
        });
    }
}
