<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AllowNullClientCurrency extends Migration
{
    public function up()
    {
        Schema::table('clients', function ($table) {
            //DB::statement('ALTER TABLE `clients` MODIFY `currency_id` INTEGER UNSIGNED NULL;');
        });
    }

    public function down() {}
}
