<?php

use Illuminate\Database\Migrations\Migration;

class AddIpToActivity extends Migration
{
    public function up()
    {
        Schema::table('activities', function ($table) {
            $table->string('ip')->nullable();
        });
    }

    public function down()
    {
        Schema::table('activities', function ($table) {
            $table->dropColumn('ip');
        });
    }
}
