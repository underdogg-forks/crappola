<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDarkMode extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->boolean('dark_mode')->default(true)->change();
        });

        Schema::table('accounts', function ($table) {
            $table->integer('credit_number_counter')->default(0)->nullable();
            $table->text('credit_number_prefix')->nullable();
            $table->text('credit_number_pattern')->nullable();
        });

        DB::statement('update users set dark_mode = 1');

        // update invoice_item_type_id for task invoice items
        DB::statement('update invoice_items
            left join invoices on invoices.id = invoice_items.invoice_id
            set invoice_item_type_id = 2
            where invoices.has_tasks = 1
            and invoice_item_type_id = 1');

        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('vendor_id')->nullable();
            $table->unsignedInteger('expense_category_id')->nullable()->index();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('invoice_currency_id')->nullable()->index();
            $table->unsignedInteger('expense_currency_id')->nullable()->index();
            $table->unsignedInteger('frequency_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('public_id')->index();
            $table->decimal('amount', 13, 2);
            $table->text('private_notes');
            $table->text('public_notes');
            $table->string('tax_name1')->nullable();
            $table->decimal('tax_rate1', 13, 3);
            $table->string('tax_name2')->nullable();
            $table->decimal('tax_rate2', 13, 3);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('last_sent_date')->nullable();

            $table->boolean('should_be_invoiced')->default(true);
            $table->boolean('is_deleted')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Relations
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_currency_id')->references('id')->on('currencies');
            $table->foreign('expense_currency_id')->references('id')->on('currencies');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');

            // Indexes
            $table->unique(['account_id', 'public_id']);
        });

        Schema::table('expenses', function ($table) {
            $table->unsignedInteger('recurring_expense_id')->nullable();
        });

        Schema::table('bank_accounts', function ($table) {
            $table->mediumInteger('app_version')->default(DEFAULT_BANK_APP_VERSION);
            $table->mediumInteger('ofx_version')->default(DEFAULT_BANK_OFX_VERSION);
        });
    }

    public function down()
    {
        Schema::drop('recurring_expenses');

        Schema::table('expenses', function ($table) {
            $table->dropColumn('recurring_expense_id');
        });

        Schema::table('accounts', function ($table) {
            $table->dropColumn('credit_number_counter');
            $table->dropColumn('credit_number_prefix');
            $table->dropColumn('credit_number_pattern');
        });

        Schema::table('bank_accounts', function ($table) {
            $table->dropColumn('app_version');
            $table->dropColumn('ofx_version');
        });
    }
}
