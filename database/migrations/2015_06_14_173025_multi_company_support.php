<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_accounts', function ($table): void {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accounts');
    }
};
