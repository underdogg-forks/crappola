<?php

use Illuminate\Database\Migrations\Migration;

class AddCascaseDrops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('invoices', function ($table): void {
            $table->dropForeign('invoices_account_id_foreign');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
    }
}
