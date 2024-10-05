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
        Schema::create('banks', function ($table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('remote_id');
            $table->integer('bank_library_id')->default(BANK_LIBRARY_OFX);
            $table->text('config');
        });

        Schema::create('bank_accounts', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('bank_id');
            $table->unsignedInteger('user_id');
            $table->string('username');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks');

            $table->unsignedInteger('public_id')->index();
            $table->unique(['account_id', 'public_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('bank_accounts');
        Schema::drop('banks');
    }
};
