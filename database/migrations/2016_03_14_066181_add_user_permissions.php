<?php

use Illuminate\Database\Migrations\Migration;

class AddUserPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function ($table): void {
            $table->boolean('is_admin')->default(true);
            $table->unsignedInteger('permissions')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function ($table): void {
            $table->dropColumn('is_admin');
            $table->dropColumn('permissions');
        });
    }
}
