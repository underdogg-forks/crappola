<?php

use Illuminate\Database\Migrations\Migration;

class AddZapierSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('subscriptions', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unsignedInteger('event_id')->nullable();
            $table->string('target_url');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
}
