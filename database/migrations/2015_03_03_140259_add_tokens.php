<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddTokens extends Migration
{
    public function up()
    {
        Schema::create('account_tokens', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('public_id')->nullable();

            $table->string('name')->nullable();
            $table->string('token')->unique();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['account_id', 'public_id']);
        });

        Schema::table('activities', function ($table) {});
    }

    public function down()
    {
        Schema::drop('account_tokens');

        Schema::table('activities', function ($table) {
            $table->dropColumn('token_id');
        });
    }
}
