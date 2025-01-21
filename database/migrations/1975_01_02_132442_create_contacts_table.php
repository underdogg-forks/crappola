<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('contacts', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('client_id')->index();
            $table->unsignedInteger('user_id');

            $table->string('contact_key')->nullable()->default(null)->index()->unique();

            $table->boolean('is_primary')->default(0);
            $table->boolean('send_invoice')->default(0);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('password', 255)->nullable();
            $table->boolean('confirmation_code', 255)->nullable();
            $table->boolean('remember_token', 100)->nullable();

            $table->timestamp('last_login')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
