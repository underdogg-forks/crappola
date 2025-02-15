<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddEmailDesigns extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('email_design_id');
            $table->dropColumn('enable_email_markup');
            $table->dropColumn('website');
        });
    }
}
