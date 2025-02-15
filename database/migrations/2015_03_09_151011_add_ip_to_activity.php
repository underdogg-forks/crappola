<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddIpToActivity extends Migration
{
    public function up()
    {
        Schema::table('activities', function ($table) {});
    }

    public function down()
    {
        Schema::table('activities', function ($table) {
            $table->dropColumn('ip');
        });
    }
}
