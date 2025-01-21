<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('payments', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('invoice_id')->index();
            $table->unsignedInteger('client_id')->index();
            $table->unsignedInteger('contact_id')->nullable();
            $table->unsignedInteger('invitation_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();

            $table->unsignedInteger('payment_status_id')->default(PAYMENT_STATUS_COMPLETED);
            $table->unsignedInteger('payment_method_id')->nullable();

            $table->unsignedInteger('account_gateway_id')->nullable();
            $table->unsignedInteger('payment_type_id')->nullable();

            $table->decimal('exchange_rate', 13, 4)->default(1);
            $table->unsignedInteger('exchange_currency_id')->nullable(false);

            $table->decimal('amount', 13, 2);
            $table->date('payment_date')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('payer_id')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('ip')->nullable();

            $table->decimal('refunded', 13, 2);

            $table->unsignedInteger('routing_number')->nullable();
            $table->smallInteger('last4')->unsigned()->nullable();
            $table->date('expiration')->nullable();
            $table->text('gateway_error')->nullable();
            $table->string('email')->nullable();

            $table->boolean('is_deleted')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreign('payment_status_id')->references('id')->on('payment_statuses');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');

            $table->foreign('account_gateway_id')->references('id')->on('account_gateways')->onDelete('cascade');
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
