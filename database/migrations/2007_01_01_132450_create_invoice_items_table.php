<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('invoice_items', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('invoice_id')->index();
            $table->unsignedInteger('product_id')->nullable();

            $table->string('product_key');
            $table->text('notes');
            $table->decimal('cost', 13, 2);
            $table->decimal('qty', 13, 2)->nullable();

            $table->decimal('discount', 13, 2)->nullable();

            $table->string('tax_name1')->nullable();
            $table->decimal('tax_rate1', 13, 3)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
