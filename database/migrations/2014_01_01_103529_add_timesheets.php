<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddTimesheets extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('user_id');

            $table->string('name');
            $table->string('description');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['company_id', 'name']);
        });

        Schema::create('project_codes', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('project_id');

            $table->string('name');
            $table->string('description');

            $table->decimal('task_rate', 12, 4)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->unique(['company_id', 'name']);
        });

        Schema::create('timesheets', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('company_id')->index();

            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->decimal('discount', 13, 2);

            $table->decimal('hours');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::create('timesheet_event_sources', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('user_id');

            $table->string('owner');
            $table->string('name');
            $table->string('url');
            $table->enum('type', ['ical', 'googlejson']);

            $table->dateTime('from_date')->nullable();
            $table->dateTime('to_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('timesheet_events', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('project_id')->nullable()->index();
            $table->unsignedInteger('project_code_id')->nullable()->index();

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('timesheet_event_source_id');
            $table->unsignedInteger('timesheet_id')->nullable()->index();

            // Basic fields
            $table->string('uid');
            $table->string('summary');
            $table->text('description');
            $table->string('location');
            $table->string('owner');
            $table->dateTime('start_date');
            $table->dateTime('end_date');

            // Calculated values
            $table->decimal('hours');
            $table->decimal('discount', 13, 2);
            $table->boolean('manualedit');

            // Original data
            $table->string('org_code');
            $table->timeStamp('org_created_at');
            $table->timeStamp('org_updated_at');
            $table->timeStamp('org_deleted_at')->default('0000-00-00T00:00:00');
            $table->string('org_start_date_timezone')->nullable();
            $table->string('org_end_date_timezone')->nullable();
            $table->text('org_data');

            // Error and merge handling
            $table->string('import_error')->nullable();
            $table->string('import_warning')->nullable();
            $table->text('updated_data')->nullable();
            $table->timeStamp('updated_data_at')->default('0000-00-00T00:00:00');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('timesheet_event_source_id')->references('id')->on('timesheet_event_sources')->onDelete('cascade');

            $table->unique(['timesheet_event_source_id', 'uid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timesheet_events');
        Schema::dropIfExists('timesheet_event_sources');
        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('project_codes');
        Schema::dropIfExists('projects');
    }
}
