<?php

use Illuminate\Database\Migrations\Migration;

class AddBluevineFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('companies', function ($table): void {
            $table->enum('bluevine_status', ['ignored', 'signed_up'])->nullable();
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
            $table->dropColumn('bluevine_status');
        });
    }
}
