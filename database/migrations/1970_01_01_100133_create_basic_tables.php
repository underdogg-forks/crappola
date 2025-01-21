<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateBasicTables extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payment_terms');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('credits');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('account_gateways');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('invoice_statuses');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('timezones');
        Schema::dropIfExists('frequencies');
        Schema::dropIfExists('date_formats');
        Schema::dropIfExists('datetime_formats');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('industries');
        Schema::dropIfExists('gateways');
        Schema::dropIfExists('payment_types');

        Schema::create('themes', function ($table): void {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('payment_types', function ($table): void {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('payment_terms', function ($table): void {
            $table->increments('id');
            $table->integer('num_days');
            $table->string('name');
        });

        Schema::create('timezones', function ($table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('location');
        });

        Schema::create('date_formats', function ($table): void {
            $table->increments('id');
            $table->string('format');
            $table->string('picker_format');
            $table->string('label');
            $table->string('format_moment');
        });

        Schema::create('datetime_formats', function ($table): void {
            $table->increments('id');
            $table->string('format');
            $table->string('label');
        });

        Schema::create('currencies', function ($table): void {
            $table->increments('id');

            $table->string('name');
            $table->string('symbol');
            $table->string('precision');
            $table->string('thousand_separator');
            $table->string('decimal_separator');
            $table->string('code');

            $table->boolean('swap_currency_symbol')->default(false);
        });

        Schema::create('sizes', function ($table): void {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('industries', function ($table): void {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('gateways', function ($table): void {
            $table->increments('id');

            $table->string('name');
            $table->string('provider');
            $table->boolean('visible')->default(true);

            $table->unsignedInteger('sort_order')->default(10000);
            $table->boolean('recommended')->default(0);
            $table->string('site_url', 200)->nullable();

            $table->timestamps();
        });

        Schema::create('account_gateways', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('gateway_id');

            $table->boolean('show_shipping_address')->default(false)->nullable();

            $table->text('config');

            $table->unsignedInteger('accepted_credit_cards')->nullable();
            $table->boolean('require_cvv')->default(true)->nullable();

            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('gateway_id')->references('id')->on('gateways');
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_resets', function ($table): void {
            $table->string('email');
            $table->string('token');

            $table->timestamps();
        });

        Schema::create('invoice_statuses', function ($table): void {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('frequencies', function ($table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('date_interval');
        });

        Schema::create('invitations', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('invoice_id')->index();
            $table->string('invitation_key')->index()->unique();

            $table->string('transaction_reference')->nullable();
            $table->timestamp('sent_date')->nullable();
            $table->timestamp('viewed_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //$table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            //$table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });

        Schema::create('credits', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('client_id')->index();
            $table->unsignedInteger('user_id');

            $table->boolean('is_deleted')->default(false);
            $table->decimal('amount', 13, 2);
            $table->decimal('balance', 13, 2);
            $table->date('credit_date')->nullable();
            $table->string('credit_number')->nullable();
            $table->text('private_notes');

            $table->timestamps();
            $table->softDeletes();

            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('activities', function ($table): void {
            $table->increments('id');

            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('contact_id')->nullable();
            $table->unsignedInteger('payment_id')->nullable();
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('credit_id')->nullable();
            $table->unsignedInteger('invitation_id')->nullable();
            $table->unsignedInteger('expense_id')->nullable();

            $table->boolean('is_system')->default(0);

            $table->string('ip')->nullable();

            $table->text('message')->nullable();
            $table->text('json_backup')->nullable();
            $table->integer('activity_type_id');
            $table->decimal('adjustment', 13, 2)->nullable();
            $table->decimal('balance', 13, 2)->nullable();

            $table->timestamps();

            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_terms');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('credits');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('account_gateways');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('invoice_statuses');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('timezones');
        Schema::dropIfExists('frequencies');
        Schema::dropIfExists('date_formats');
        Schema::dropIfExists('datetime_formats');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('industries');
        Schema::dropIfExists('gateways');
        Schema::dropIfExists('payment_types');
    }
}
