<?php

use Illuminate\Database\Migrations\Migration;

class AddSupportForBots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('security_codes', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('contact_id')->nullable();
            $table->smallInteger('attempts');
            $table->string('code')->nullable();
            $table->string('bot_user_id')->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('security_codes', function ($table): void {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::table('users', function ($table): void {
            $table->string('bot_user_id')->nullable();
        });

        Schema::table('contacts', function ($table): void {
            $table->string('bot_user_id')->nullable();
        });

        Schema::table('companies', function ($table): void {
            $table->boolean('include_item_taxes_inline')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('security_codes');

        Schema::table('users', function ($table): void {
            $table->dropColumn('bot_user_id');
        });

        Schema::table('contacts', function ($table): void {
            $table->dropColumn('bot_user_id');
        });

        Schema::table('companies', function ($table): void {
            $table->dropColumn('include_item_taxes_inline');
        });
    }
}
