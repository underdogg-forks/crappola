<?php

use Illuminate\Database\Migrations\Migration;

class AddCustomDesign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->mediumText('custom_design')->nullable();
        });

        DB::table('invoice_designs')->insert(['id' => CUSTOM_DESIGN1, 'name' => 'Custom']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('companies', function ($table): void {
            $table->dropColumn('custom_design');
        });
    }
}
