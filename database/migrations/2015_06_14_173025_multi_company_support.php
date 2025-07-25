<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class MultiCompanySupport extends Migration
{
    public function up()
    {
        Schema::create('user_accounts', function ($table) {
            $table->increments('id');

            $table->unsignedInteger('user_id1')->nullable();
            $table->unsignedInteger('user_id2')->nullable();
            $table->unsignedInteger('user_id3')->nullable();
            $table->unsignedInteger('user_id4')->nullable();
            $table->unsignedInteger('user_id5')->nullable();

            $table->foreign('user_id1')->references('id')->on('users');
            $table->foreign('user_id2')->references('id')->on('users');
            $table->foreign('user_id3')->references('id')->on('users');
            $table->foreign('user_id4')->references('id')->on('users');
            $table->foreign('user_id5')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_accounts');
    }
}
