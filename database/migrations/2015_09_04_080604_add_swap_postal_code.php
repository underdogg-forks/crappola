<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddSwapPostalCode extends Migration
{
    public function up()
    {
        Schema::table('countries', function ($table) {});

        Schema::table('accounts', function ($table) {});
    }

    public function down()
    {
        Schema::table('countries', function ($table) {
            $table->dropColumn('swap_postal_code');
        });

        Schema::table('accounts', function ($table) {
            $table->dropColumn('show_item_taxes');
        });
    }
}
