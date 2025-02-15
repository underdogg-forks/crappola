<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddReminderSettings extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('direction_reminder1');
            $table->dropColumn('direction_reminder2');
            $table->dropColumn('direction_reminder3');

            $table->dropColumn('field_reminder1');
            $table->dropColumn('field_reminder2');
            $table->dropColumn('field_reminder3');
        });
    }
}
