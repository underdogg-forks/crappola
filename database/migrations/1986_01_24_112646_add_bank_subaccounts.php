<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddBankSubaccounts extends Migration
{
    public function up(): void
    {
        Schema::create('bank_subaccounts', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('bank_account_id');

            $table->string('company_name');
            $table->string('account_number');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bank_account_id')->references('id')->on('companies_banks')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::drop('bank_subaccounts');
    }
}
