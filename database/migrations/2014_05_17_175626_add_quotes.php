<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddQuotes extends Migration
{
    public function up()
    {
        Schema::table('invoices', function ($table) {});
    }

    public function down()
    {
        Schema::table('invoices', function ($table) {
            $table->dropColumn('invoice_type_id');
            $table->dropColumn('quote_id');
            $table->dropColumn('quote_invoice_id');
        });
    }
}
