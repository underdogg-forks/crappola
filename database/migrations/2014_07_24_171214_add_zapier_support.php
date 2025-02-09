<?php

use Illuminate\Database\Migrations\Migration;

class AddZapierSupport extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->nullable();

            $table->unsignedInteger('event_id')->nullable();
            $table->string('target_url');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
