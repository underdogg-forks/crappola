<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddAppStoreOrderId extends Migration
{
    public function up()
    {
        Schema::table('companies', function ($table) {
            $table->string('app_store_order_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('companies', function ($table) {
            $table->dropColumn('app_store_order_id');
        });
    }
}
