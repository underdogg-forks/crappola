<?php

use Illuminate\Database\Migrations\Migration;

class AddCompanyPlanVatNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->string('vat_number')->nullable();
        });

        Schema::table('clients', function ($table): void {
            $table->string('vat_number')->nullable();
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
            $table->dropColumn('vat_number');
        });

        Schema::table('clients', function ($table): void {
            $table->dropColumn('vat_number');
        });
    }
}
