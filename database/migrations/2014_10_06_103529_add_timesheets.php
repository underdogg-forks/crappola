<?php
use Illuminate\Database\Migrations\Migration;

class AddTimesheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('company_id')->index();
            $t->unsignedInteger('customer_id')->nullable();
            $t->string('name');
            $t->string('description');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            //$t->foreign('company_id')->references('id')->on('companies');
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'name']);
        });
        Schema::create('project_codes', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('company_id')->index();
            $t->unsignedInteger('project_id');
            $t->unsignedInteger('staff_id');
            $t->string('name');
            $t->string('description');
            $t->timestamps();
            $t->softDeletes();
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            //$t->foreign('company_id')->references('id')->on('companies');
            //$t->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $t->unique(['company_id', 'name']);
        });
        Schema::create('timesheets', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('company_id')->index();
            $t->dateTime('start_date');
            $t->dateTime('end_date');
            $t->float('discount');
            $t->decimal('hours');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            //$t->foreign('company_id')->references('id')->on('companies');
            $t->unsignedInteger('public_id');
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'public_id']);
        });
        Schema::create('timesheet_event_sources', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('company_id')->index();
            $t->string('owner');
            $t->string('name');
            $t->string('url');
            $t->enum('type', ['ical', 'googlejson']);
            $t->dateTime('from_date')->nullable();
            $t->dateTime('to_date')->nullable();
            $t->timestamps();
            $t->softDeletes();
            //$t->foreign('company_id')->references('id')->on('companies');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
        Schema::create('timesheet_events', function ($t) {
            $t->increments('id');
            $t->unsignedInteger('staff_id');
            $t->unsignedInteger('company_id')->index();
            $t->unsignedInteger('timesheet_event_source_id');
            $t->unsignedInteger('timesheet_id')->nullable()->index();
            $t->unsignedInteger('project_id')->nullable()->index();
            $t->unsignedInteger('project_code_id')->nullable()->index();
            // Basic fields
            $t->string('uid');
            $t->string('summary');
            $t->text('description');
            $t->string('location');
            $t->string('owner');
            $t->dateTime('start_date');
            $t->dateTime('end_date');
            // Calculated values
            $t->decimal('hours');
            $t->float('discount');
            $t->boolean('manualedit');
            // Original data
            $t->string('org_code');
            $t->timeStamp('org_created_at');
            $t->timeStamp('org_updated_at');
            $t->timeStamp('org_deleted_at')->default('0000-00-00T00:00:00');
            $t->string('org_start_date_timezone')->nullable();
            $t->string('org_end_date_timezone')->nullable();
            $t->text('org_data');
            // Error and merge handling
            $t->string('import_error')->nullable();
            $t->string('import_warning')->nullable();
            $t->text('updated_data')->nullable();
            $t->timeStamp('updated_data_at')->default('0000-00-00T00:00:00');
            //$t->foreign('company_id')->references('id')->on('companies');
            //$t->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            //$t->foreign('timesheet_event_source_id')->references('id')->on('timesheet_event_sources')->onDelete('cascade');
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['timesheet_event_source_id', 'uid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timesheet_events');
        Schema::dropIfExists('timesheet_event_sources');
        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('project_codes');
        Schema::dropIfExists('projects');
    }
}
