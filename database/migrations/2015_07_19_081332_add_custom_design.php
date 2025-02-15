<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCustomDesign extends Migration
{
    public function up()
    {
        DB::table('invoice_designs')->insert(['id' => CUSTOM_DESIGN1, 'name' => 'Custom']);
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('custom_design');
        });
    }
}
