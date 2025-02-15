<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class LimitNotifications extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->boolean('only_notify_owned')->nullable()->default(false);
        });
    }

    public function down() {}
}
