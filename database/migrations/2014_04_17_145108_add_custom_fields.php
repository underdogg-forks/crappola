<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddCustomFields extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});

        Schema::table('clients', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('custom_label1');
            $table->dropColumn('custom_value1');

            $table->dropColumn('custom_label2');
            $table->dropColumn('custom_value2');

            $table->dropColumn('custom_client_label1');
            $table->dropColumn('custom_client_label2');
        });

        Schema::table('clients', function ($table) {
            $table->dropColumn('custom_value1');
            $table->dropColumn('custom_value2');
        });
    }
}
