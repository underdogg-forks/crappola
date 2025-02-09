<?php

use Illuminate\Database\Migrations\Migration;

class AddPdfmakeSupport extends Migration
{
    public function up()
    {
        Schema::table('invoice_designs', function ($table) {
            $table->mediumText('pdfmake')->nullable();
        });
    }

    public function down()
    {
        Schema::table('invoice_designs', function ($table) {
            $table->dropColumn('pdfmake');
        });
    }
}
