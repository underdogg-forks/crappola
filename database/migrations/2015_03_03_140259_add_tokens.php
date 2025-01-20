<?php

use Illuminate\Database\Migrations\Migration;

class AddTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('account_tokens', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('name')->nullable();
            $table->string('token')->unique();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('public_id')->nullable();
            $table->unique(['company_id', 'public_id']);
        });

        Schema::table('activities', function ($table): void {
            $table->unsignedInteger('token_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('account_tokens');

        Schema::table('activities', function ($table): void {
            $table->dropColumn('token_id');
        });
    }
}
