<?php

use Illuminate\Database\Migrations\Migration;

class AddPhoneToAccount extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->string('work_phone')->nullable();
            $table->string('work_email')->nullable();
        });
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('work_phone');
            $table->dropColumn('work_email');
        });
    }
}
