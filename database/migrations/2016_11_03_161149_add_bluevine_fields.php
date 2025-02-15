<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddBluevineFields extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->enum('bluevine_status', ['ignored', 'signed_up'])->nullable();
        });
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('bluevine_status');
        });
    }
}
