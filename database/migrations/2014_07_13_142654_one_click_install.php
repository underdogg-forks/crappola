<?php

use Illuminate\Database\Migrations\Migration;

class OneClickInstall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('affiliates', function ($table): void {
            $table->increments('id');

            $table->string('name', 100);
            $table->string('affiliate_key')->unique();

            $table->text('payment_title');
            $table->text('payment_subtitle');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('licenses', function ($table): void {
            $table->increments('id');

            $table->unsignedInteger('affiliate_id');

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');

            $table->string('license_key')->unique();
            $table->boolean('is_claimed');
            $table->string('transaction_reference');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('affiliate_id')->references('id')->on('affiliates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
        Schema::dropIfExists('affiliates');
    }
}
