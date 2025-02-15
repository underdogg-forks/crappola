<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddProductsSettings extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->boolean('fill_products')->after('website')->default(true);
            $table->boolean('update_products')->after('fill_products')->default(true);
        });

        DB::table('accounts')->update(['fill_products' => true]);
        DB::table('accounts')->update(['update_products' => true]);
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('fill_products');
            $table->dropColumn('update_products');
        });
    }
}
