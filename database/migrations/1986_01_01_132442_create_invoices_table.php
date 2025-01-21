<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('client_id')->index();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('invoice_status_id')->default(1);
            $table->unsignedInteger('recurring_invoice_id')->index()->nullable();

            $table->unsignedInteger('invoice_type_id');
            $table->unsignedInteger('quote_id')->nullable();
            $table->unsignedInteger('quote_invoice_id')->nullable();

            $table->string('invoice_number');
            $table->decimal('discount', 13, 2)->nullable();
            $table->string('po_number');
            $table->date('invoice_date')->nullable();
            $table->date('due_at')->nullable();
            $table->date('partial_due_date')->nullable();
            $table->text('terms');
            $table->text('public_notes');

            $table->text('invoice_footer')->nullable();

            $table->boolean('has_expenses')->default(false);

            $table->boolean('is_recurring')->default(false);
            $table->unsignedInteger('frequency_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('last_sent_date')->nullable();

            $table->string('tax_name1');
            $table->decimal('tax_rate1', 13, 3);

            $table->decimal('amount', 13, 2);
            $table->decimal('balance', 13, 2);
            $table->decimal('partial', 13, 2)->nullable();

            $table->boolean('is_amount_discount')->nullable();

            $table->boolean('is_deleted')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_status_id')->references('id')->on('invoice_statuses');
            $table->foreign('recurring_invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
