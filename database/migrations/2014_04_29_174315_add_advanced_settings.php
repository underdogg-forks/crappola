<?php

use Illuminate\Database\Migrations\Migration;

class AddAdvancedSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
        });

        Schema::table('payments', function ($table): void {
            $table->dropForeign('payments_invoice_id_foreign');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
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
            $table->dropColumn('primary_color');
            $table->dropColumn('secondary_color');
        });
    }
}
