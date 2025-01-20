<?php

use Illuminate\Database\Migrations\Migration;

class AddFontSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->smallInteger('font_size')->default(DEFAULT_FONT_SIZE);
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
            $table->dropColumn('font_size');
        });
    }
}
