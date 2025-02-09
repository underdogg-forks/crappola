<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePaymentLibraries extends Migration
{
    public function up()
    {
        Schema::dropIfExists('payment_libraries');

        Schema::create('payment_libraries', function ($t) {
            $t->increments('id');

            $t->string('name');
            $t->boolean('visible')->default(true);
            $t->timestamps();
        });

        Schema::table('gateways', function ($table) {
            $table->unsignedInteger('payment_library_id')->default(1);
        });

        DB::table('gateways')->update(['payment_library_id' => 1]);

        Schema::table('gateways', function ($table) {
            $table->foreign('payment_library_id')->references('id')->on('payment_libraries')->onDelete('cascade');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('gateways', 'payment_library_id')) {
            Schema::table('gateways', function ($table) {
                $table->dropForeign('gateways_payment_library_id_foreign');
                $table->dropColumn('payment_library_id');
            });
        }

        Schema::dropIfExists('payment_libraries');
    }
}
