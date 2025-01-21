<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateScheduledReportsTable extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_reports', function ($table): void {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('company_id')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->text('config');
            $table->enum('frequency', ['daily', 'weekly', 'biweekly', 'monthly']);
            $table->date('send_date');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_reports');
    }
}
