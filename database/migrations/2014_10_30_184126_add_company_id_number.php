<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdNumber extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});

        Schema::table('clients', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('id_number');
        });
        Schema::table('clients', function ($table) {
            $table->dropColumn('id_number');
        });
    }
}
