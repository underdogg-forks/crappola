<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddNotifyApproved extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {});
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('notify_approved');
        });
    }
}
