<?php

use Illuminate\Database\Migrations\Migration;

class AddCompanyVatNumber extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->string('vat_number')->nullable();
        });

        Schema::table('clients', function ($table) {
            $table->string('vat_number')->nullable();
        });
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('vat_number');
        });

        Schema::table('clients', function ($table) {
            $table->dropColumn('vat_number');
        });
    }
}
