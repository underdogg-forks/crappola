<?php

use Illuminate\Database\Migrations\Migration;

class AddIpToActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('activities', function ($table): void {
            $table->string('ip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('activities', function ($table): void {
            $table->dropColumn('ip');
        });
    }
}
