<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('users', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('theme_id')->nullable();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->string('confirmation_code')->nullable();
            $table->boolean('registered')->default(false);
            $table->boolean('confirmed')->default(false);

            $table->string('oauth_user_id')->nullable();
            $table->unsignedInteger('oauth_provider_id')->nullable();

            $table->string('google_2fa_secret')->nullable();

            $table->boolean('only_notify_owned')->nullable()->default(false);

            $table->boolean('notify_sent')->default(true);
            $table->boolean('notify_viewed')->default(false);
            $table->boolean('notify_paid')->default(true);
            $table->boolean('notify_approved')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
