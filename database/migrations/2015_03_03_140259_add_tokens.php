<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddTokens extends Migration
{
    public function up(): void
    {
        Schema::create('account_tokens', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('user_id');

            $table->string('name')->nullable();
            $table->string('token')->unique();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('activities', function ($table): void {
            $table->unsignedInteger('token_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::drop('account_tokens');

        Schema::table('activities', function ($table): void {
            $table->dropColumn('token_id');
        });
    }
}
