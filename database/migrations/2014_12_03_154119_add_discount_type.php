<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddDiscountType extends Migration
{
    public function up()
    {
        Schema::table('invoices', function ($table) {});
    }

    public function down()
    {
        Schema::table('invoices', function ($table) {
            $table->dropColumn('is_amount_discount');
        });
    }
}
