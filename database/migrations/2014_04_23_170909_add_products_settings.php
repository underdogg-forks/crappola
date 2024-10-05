<?php

use Illuminate\Database\Migrations\Migration;

class AddProductsSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('accounts', function ($table): void {
            $table->boolean('fill_products')->default(true);
            $table->boolean('update_products')->default(true);
        });

        DB::table('accounts')->update(['fill_products' => true]);
        DB::table('accounts')->update(['update_products' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('accounts', function ($table): void {
            $table->dropColumn('fill_products');
            $table->dropColumn('update_products');
        });
    }
}
