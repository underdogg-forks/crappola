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
        Schema::table('accounts', function ($table): void {
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
        Schema::table('accounts', function ($table): void {
            $table->dropColumn('vat_number');
        });

        Schema::table('clients', function ($table): void {
            $table->dropColumn('vat_number');
        });
    }
};
