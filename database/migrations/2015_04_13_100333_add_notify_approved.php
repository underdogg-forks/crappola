<?php

use Illuminate\Database\Migrations\Migration;

class AddNotifyApproved extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->boolean('notify_approved')->default(true);
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('notify_approved');
        });
    }
}
