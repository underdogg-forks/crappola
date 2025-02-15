<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddTasks extends Migration
{
    public function up()
    {
        Schema::create('tasks', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('public_id')->index();

            $table->timestamp('start_time')->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('is_running')->default(false);
            $table->integer('break_duration')->nullable();
            $table->timestamp('resume_time')->nullable();
            $table->text('time_log')->nullable();
            $table->string('description')->nullable();

            $table->text('custom_value1')->nullable();
            $table->text('custom_value2')->nullable();

            $table->boolean('is_deleted')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->unique(['account_id', 'public_id']);
        });

        //Schema::dropIfExists('timesheets');
        //Schema::dropIfExists('timesheet_events');
        //Schema::dropIfExists('timesheet_event_sources');
        //Schema::dropIfExists('project_codes');
        //Schema::dropIfExists('projects');
    }

    public function down()
    {
        Schema::drop('tasks');
    }
}
