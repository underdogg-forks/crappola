<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddBankAccounts extends Migration
{
    public function up(): void
    {
        Schema::create('banks', function ($table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('remote_id');
            $table->unsignedInteger('bank_library_id')->default(BANK_LIBRARY_OFX);
            $table->text('config');
        });

        Schema::create('companies_banks', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('bank_id');
            $table->unsignedInteger('user_id');
            $table->string('username');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks');
        });
    }

    public function down(): void
    {
        Schema::drop('companies_banks');
        Schema::drop('banks');
    }
}
