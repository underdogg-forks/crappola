<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class SupportLockingAccount extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {});

        Schema::table('account_gateways', function ($table) {});
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('failed_logins');
        });

        Schema::table('account_gateways', function ($table) {
            $table->dropColumn('show_address');
            $table->dropColumn('update_address');
        });
    }
}
