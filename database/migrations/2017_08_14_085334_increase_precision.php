<?php

use Illuminate\Database\Migrations\Migration;

class IncreasePrecision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        /* Schema::table('products', function ($table): void {
            $table->decimal('cost', 15, 4)->change();
            $table->decimal('qty', 15, 4)->default(0)->change();
        }); */

        /* Schema::table('invoice_items', function ($table): void {
            $table->decimal('cost', 15, 4)->change();
            $table->decimal('qty', 15, 4)->default(0)->change();
        }); */

        Schema::table('clients', function ($table): void {
            $table->integer('credit_number_counter')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('clients', function ($table): void {
            $table->dropColumn('credit_number_counter');
        });
    }
}
