<?php

use Illuminate\Database\Migrations\Migration;

class AddPageSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->string('page_size')->default('A4');
            $table->boolean('live_preview')->default(true);
            $table->smallInteger('invoice_number_padding')->default(4);
        });

        Schema::table('fonts', function ($table): void {
            $table->dropColumn('is_early_access');
        });

        Schema::create('expense_categories', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('company_id')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')->nullable();
            $table->unsignedInteger('public_id')->index();
        });

        Schema::table('expense_categories', function ($table): void {
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['company_id', 'public_id']);
        });

        Schema::table('expenses', function ($table): void {
            $table->unsignedInteger('expense_category_id')->nullable()->index();
        });

        Schema::table('expenses', function ($table): void {
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('companies', function ($table): void {
            $table->dropColumn('page_size');
            $table->dropColumn('live_preview');
            $table->dropColumn('invoice_number_padding');
        });

        Schema::table('fonts', function ($table): void {
            $table->boolean('is_early_access');
        });

        Schema::table('expenses', function ($table): void {
            $table->dropForeign('expenses_expense_category_id_foreign');
            $table->dropColumn('expense_category_id');
        });

        Schema::dropIfExists('expense_categories');
    }
}
