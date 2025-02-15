<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddBuyNowButtons extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});

        Schema::table('datetime_formats', function ($table) {});

        Schema::table('date_formats', function ($table) {});
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('enable_buy_now_buttons');
            $table->text('invoice_design')->nullable();
        });

        Schema::table('datetime_formats', function ($table) {
            $table->string('label');
        });

        Schema::table('date_formats', function ($table) {
            $table->string('label');
        });
    }
}
