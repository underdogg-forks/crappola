<?php

use Illuminate\Database\Migrations\Migration;

class AddAffiliatePrice extends Migration
{
    public function up()
    {
        Schema::table('affiliates', function ($table) {
            $table->decimal('price', 7, 2)->nullable();
        });

        Schema::table('licenses', function ($table) {
            $table->unsignedInteger('product_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('affiliates', function ($table) {
            $table->dropColumn('price');
        });

        Schema::table('licenses', function ($table) {
            $table->dropColumn('product_id');
        });
    }
}
