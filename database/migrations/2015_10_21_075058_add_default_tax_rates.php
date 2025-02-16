<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddDefaultTaxRates extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});

        Schema::table('products', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('default_tax_rate_id');
            $table->dropColumn('recurring_hour');
        });

        Schema::table('products', function ($table) {
            $table->dropColumn('default_tax_rate_id');
        });
    }
}
