<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddTaskProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {

        Schema::create('tasks', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('invoice_id')->nullable();
            $table->integer('product_id')->nullable();

            $table->timestamp('start_at')->nullable();
            $table->integer('duration')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_deleted')->default(false);

            $table->timestamps();
            $table->softDeletes();


            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //$table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            //$table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->unsignedInteger('public_id')->index();
            $table->unique(['company_id', 'public_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
    }
}
