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
        Schema::table('tax_rates', function ($table): void {
            if (Schema::hasColumn('tax_rates', 'rate')) {
                $table->decimal('rate', 13, 3)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('tax_rates', function ($table): void {
            $table->decimal('rate', 13, 2)->change();
        });
    }
};
