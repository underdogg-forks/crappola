<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddDocuments extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function ($table): void {
            $table->increments('id');

            $table->unsignedInteger('company_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('expense_id')->nullable();
            $table->unsignedInteger('ticket_id')->nullable();

            $table->string('document_key')->nullable()->unique();
            $table->string('name');

            $table->boolean('is_proposal')->default(false);

            $table->string('path');
            $table->string('preview');

            $table->string('type');
            $table->string('disk');
            $table->string('hash', 40);
            $table->unsignedInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
}
