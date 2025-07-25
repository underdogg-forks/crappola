<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddBankAccounts extends Migration
{
    public function up()
    {
        Schema::create('banks', function ($table) {
            $table->increments('id');
            $table->integer('bank_library_id')->default(BANK_LIBRARY_OFX);
            $table->string('name');
            $table->string('remote_id');
            $table->text('config');
        });

        Schema::create('bank_accounts', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('bank_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('public_id')->index();

            $table->string('username');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks');

            $table->unique(['account_id', 'public_id']);
        });
    }

    public function down()
    {
        Schema::drop('bank_accounts');
        Schema::drop('banks');
    }
}
