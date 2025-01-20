<?php

use Illuminate\Database\Migrations\Migration;

class AddSwapPostalCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('countries', function ($table): void {
            $table->boolean('swap_postal_code')->default(0);
        });

        Schema::table('companies', function ($table): void {
            $table->boolean('show_item_taxes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('countries', function ($table): void {
            $table->dropColumn('swap_postal_code');
        });

        Schema::table('companies', function ($table): void {
            $table->dropColumn('show_item_taxes');
        });
    }
}
