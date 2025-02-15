<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class ImproveCurrencyLocalization extends Migration
{
    public function up()
    {
        Schema::table('countries', function ($table) {});
    }

    public function down()
    {
        Schema::table('countries', function ($table) {
            $table->dropColumn('swap_currency_symbol');
            $table->dropColumn('thousand_separator');
            $table->dropColumn('decimal_separator');
        });
    }
}
