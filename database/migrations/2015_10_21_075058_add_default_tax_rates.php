<?php

use Illuminate\Database\Migrations\Migration;

class AddDefaultTaxRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('accounts', function ($table): void {
            $table->unsignedInteger('default_tax_rate_id')->nullable();
            $table->smallInteger('recurring_hour')->default(DEFAULT_SEND_RECURRING_HOUR);
        });

        Schema::table('products', function ($table): void {
            $table->unsignedInteger('default_tax_rate_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('accounts', function ($table): void {
            $table->dropColumn('default_tax_rate_id');
            $table->dropColumn('recurring_hour');
        });

        Schema::table('products', function ($table): void {
            $table->dropColumn('default_tax_rate_id');
        });
    }
}
