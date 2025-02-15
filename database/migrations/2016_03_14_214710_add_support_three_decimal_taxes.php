<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddSupportThreeDecimalTaxes extends Migration
{
    public function up()
    {
        Schema::table('tax_rates', function ($table) {
            if (Schema::hasColumn('tax_rates', 'rate')) {
                $table->decimal('rate', 13, 3)->change();
            }
        });
    }

    public function down()
    {
        Schema::table('tax_rates', function ($table) {
            $table->decimal('rate', 13, 2)->change();
        });
    }
}
