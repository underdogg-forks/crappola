<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddAffiliatePrice extends Migration
{
    public function up()
    {
        Schema::table('affiliates', function ($table) {
            $table->decimal('price', 7, 2)->nullable();
        });

        Schema::table('licenses', function ($table) {
            $table->unsignedInteger('product_id')->after('affiliate_id')->nullable();
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
