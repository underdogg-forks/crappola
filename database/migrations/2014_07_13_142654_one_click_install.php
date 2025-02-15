<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class OneClickInstall extends Migration
{
    public function up()
    {
        Schema::create('affiliates', function ($table) {
            $table->increments('id');

            $table->string('name');
            $table->string('affiliate_key')->unique();

            $table->text('payment_title');
            $table->text('payment_subtitle');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('licenses', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('affiliate_id');

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');

            $table->string('license_key')->unique();
            $table->boolean('is_claimed');
            $table->string('transaction_reference');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('affiliate_id')->references('id')->on('affiliates');
        });
    }

    public function down()
    {
        Schema::dropIfExists('licenses');
        Schema::dropIfExists('affiliates');
    }
}
