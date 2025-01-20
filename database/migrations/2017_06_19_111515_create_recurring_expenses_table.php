<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecurringExpensesTable extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_expenses', function (Blueprint $table): void {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('vendor_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('client_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->decimal('amount', 13, 2);
            $table->text('private_notes');
            $table->text('public_notes');
            $table->unsignedInteger('invoice_currency_id')->nullable()->index();
            $table->boolean('should_be_invoiced')->default(true);
            $table->unsignedInteger('expense_category_id')->nullable()->index();
            $table->string('tax_name1')->nullable();
            $table->decimal('tax_rate1', 13, 3);
            $table->string('tax_name2')->nullable();
            $table->decimal('tax_rate2', 13, 3);

            $table->unsignedInteger('frequency_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('last_sent_date')->nullable();

            // Relations
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_currency_id')->references('id')->on('currencies');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
        });

        Schema::table('expenses', function ($table): void {
            $table->unsignedInteger('recurring_expense_id')->nullable();
        });

        Schema::table('companies_banks', function ($table): void {
            $table->mediumInteger('app_version')->default(DEFAULT_BANK_APP_VERSION);
            $table->mediumInteger('ofx_version')->default(DEFAULT_BANK_OFX_VERSION);
        });
    }

    public function down(): void
    {
        Schema::drop('recurring_expenses');

        Schema::table('expenses', function ($table): void {
            $table->dropColumn('recurring_expense_id');
        });

        Schema::table('companies_banks', function ($table): void {
            $table->dropColumn('app_version');
            $table->dropColumn('ofx_version');
        });
    }
}
