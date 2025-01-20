<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeeCapToAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('account_gateway_settings', function (Blueprint $table): void {
            $table->integer('fee_cap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('account_gateway_settings', function (Blueprint $table): void {
            $table->dropColumn('fee_cap');
        });
    }
}
