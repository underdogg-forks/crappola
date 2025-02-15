<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddPdfmakeSupport extends Migration
{
    public function up()
    {
        Schema::table('invoice_designs', function ($table) {});
    }

    public function down()
    {
        Schema::table('invoice_designs', function ($table) {
            $table->dropColumn('pdfmake');
        });
    }
}
