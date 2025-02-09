<?php

use Illuminate\Database\Migrations\Migration;

class AddQuotes extends Migration
{
    public function up()
    {
        Schema::table('invoices', function ($table) {
            $table->boolean('invoice_type_id')->default(0);
            $table->unsignedInteger('quote_id')->nullable();
            $table->unsignedInteger('quote_invoice_id')->nullable();
        });
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
