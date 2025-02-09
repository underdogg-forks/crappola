<?php

use Illuminate\Database\Migrations\Migration;

class AddPdfmakeSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('invoice_designs', function ($table): void {
            $table->mediumText('pdfmake')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invoice_designs', function ($table): void {
            $table->dropColumn('pdfmake');
        });
    }
}
