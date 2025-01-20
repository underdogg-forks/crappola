<?php

use Illuminate\Database\Migrations\Migration;

class AddAffiliatePrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('affiliates', function ($table): void {
            $table->decimal('price', 7, 2)->nullable();
        });

        Schema::table('licenses', function ($table): void {
            $table->unsignedInteger('product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('affiliates', function ($table): void {
            $table->dropColumn('price');
        });

        Schema::table('licenses', function ($table): void {
            $table->dropColumn('product_id');
        });
    }
}
