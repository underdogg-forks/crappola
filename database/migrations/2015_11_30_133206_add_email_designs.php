<?php

use Illuminate\Database\Migrations\Migration;

class AddEmailDesigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->smallInteger('email_design_id')->default(1);
            $table->boolean('enable_email_markup')->default(false);
            $table->string('website')->nullable();
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
            $table->dropColumn('email_design_id');
            $table->dropColumn('enable_email_markup');
            $table->dropColumn('website');
        });
    }
}
