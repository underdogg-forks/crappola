<?php

use Illuminate\Database\Migrations\Migration;

class AddUserPermissions extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->boolean('is_admin')->default(true);
            $table->unsignedInteger('permissions')->default(0);
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('is_admin');
            $table->dropColumn('permissions');
        });
    }
}
