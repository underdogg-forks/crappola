<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddBankSubaccounts extends Migration
{
    public function up()
    {
        Schema::create('bank_subaccounts', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('bank_account_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('public_id')->index();

            $table->string('account_name');
            $table->string('account_number');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade');

            $table->unique(['account_id', 'public_id']);
        });

        Schema::table('expenses', function ($table) {});

        Schema::table('vendors', function ($table) {});
    }

    public function down()
    {
        Schema::drop('bank_subaccounts');

        Schema::table('expenses', function ($table) {
            $table->dropColumn('transaction_id');
            $table->dropColumn('bank_id');
        });

        Schema::table('vendors', function ($table) {
            $table->dropColumn('transaction_name');
        });
    }
}
