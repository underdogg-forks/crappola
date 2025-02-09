<?php

use Illuminate\Database\Migrations\Migration;

class IncreasePrecision extends Migration
{
    public function up()
    {
        Schema::table('products', function ($table) {
            $table->decimal('cost', 15, 4)->change();
            $table->decimal('qty', 15, 4)->default(0)->change();
        });

        Schema::table('invoice_items', function ($table) {
            $table->decimal('cost', 15, 4)->change();
            $table->decimal('qty', 15, 4)->default(0)->change();
        });

        Schema::table('clients', function ($table) {
            $table->integer('credit_number_counter')->default(1)->nullable();
        });
    }

    public function down()
    {
        Schema::table('clients', function ($table) {
            $table->dropColumn('credit_number_counter');
        });
    }
}
