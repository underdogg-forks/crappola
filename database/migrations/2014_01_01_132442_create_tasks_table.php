<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('project_id')->nullable()->index();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedInteger('user_id')->index();

            $table->unsignedInteger('task_status_id')->index()->nullable();

            $table->timestamp('start_at')->nullable();
            $table->integer('duration')->nullable();
            $table->string('description')->nullable();

            $table->smallInteger('task_status_sort_order')->default(0);

            $table->boolean('is_deleted')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
    }
}
