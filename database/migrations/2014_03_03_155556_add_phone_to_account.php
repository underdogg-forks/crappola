<?php

use Illuminate\Database\Migrations\Migration;

class AddPhoneToAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->string('work_phone')->nullable();
            $table->string('work_email')->nullable();
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
            $table->dropColumn('work_phone');
            $table->dropColumn('work_email');
        });
    }
}
