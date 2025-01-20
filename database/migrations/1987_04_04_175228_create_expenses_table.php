<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('currency_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->string('name')->nullable();
            $table->string('address1');
            $table->string('address2');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->unsignedInteger('country_id')->nullable();
            $table->string('work_phone');
            $table->text('private_notes');
            $table->string('website');

            $table->string('vat_number')->nullable();
            $table->string('id_number')->nullable();

            $table->string('transaction_name')->nullable();

            $table->tinyInteger('is_deleted')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries');
        });

        Schema::create('vendor_contacts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('vendor_id')->index();
            $table->unsignedInteger('user_id');

            $table->boolean('is_primary')->default(0);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('expenses', function (Blueprint $table): void {
            $table->increments('id');

            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('vendor_id')->nullable();
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();

            $table->unsignedInteger('expense_category_id')->nullable()->index();

            $table->unsignedInteger('user_id');
            $table->boolean('is_deleted')->default(false);
            $table->decimal('amount', 13, 2);
            $table->decimal('foreign_amount', 13, 2);
            $table->decimal('exchange_rate', 13, 4);
            $table->date('expense_date')->nullable();
            $table->text('private_notes');
            $table->text('public_notes');
            $table->unsignedInteger('invoice_currency_id')->nullable(false);

            $table->decimal('exchange_rate', 13, 4)->default(1)->change();

            $table->string('transaction_id')->nullable();
            $table->unsignedInteger('bank_id')->nullable();

            $table->boolean('should_be_invoiced')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Relations
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->foreign('invoice_currency_id')->references('id')->on('currencies');
        });
    }

    public function down(): void
    {
        Schema::drop('expenses');
        Schema::drop('vendor_contacts');
        Schema::drop('vendors');
    }
}
