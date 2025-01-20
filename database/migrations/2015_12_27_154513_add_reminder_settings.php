<?php

use Illuminate\Database\Migrations\Migration;

class AddReminderSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->smallInteger('direction_reminder1')->default(1);
            $table->smallInteger('direction_reminder2')->default(1);
            $table->smallInteger('direction_reminder3')->default(1);

            $table->smallInteger('field_reminder1')->default(1);
            $table->smallInteger('field_reminder2')->default(1);
            $table->smallInteger('field_reminder3')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('companies', function ($table): void {
            $table->dropColumn('direction_reminder1');
            $table->dropColumn('direction_reminder2');
            $table->dropColumn('direction_reminder3');

            $table->dropColumn('field_reminder1');
            $table->dropColumn('field_reminder2');
            $table->dropColumn('field_reminder3');
        });
    }
}
