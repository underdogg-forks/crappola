<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->float('discount');
            $table->date('discount_expires')->nullable();
            $table->date('promo_expires')->nullable();
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
            $table->dropColumn('discount');
            $table->dropColumn('discount_expires');
            $table->dropColumn('promo_expires');
        });
    }
};
