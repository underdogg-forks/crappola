<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration
{
    public function up()
    {
        //Schema::rename('password_reminders', 'password_resets');
    }

    public function down()
    {
        Schema::rename('password_resets', 'password_reminders');
    }
}
