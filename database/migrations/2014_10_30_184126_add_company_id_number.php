<?php

use Illuminate\Database\Migrations\Migration;

class AddCompanyPlanIdNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->string('id_number')->nullable();
        });

        Schema::table('clients', function ($table): void {
            $table->string('id_number')->nullable();
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
            $table->dropColumn('id_number');
        });
        Schema::table('clients', function ($table): void {
            $table->dropColumn('id_number');
        });
    }
}
