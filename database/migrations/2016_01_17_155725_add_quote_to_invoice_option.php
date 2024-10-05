<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    /**
     * Run the migrations.
     * Make the conversion of a quote into an invoice automatically after a client approves optional.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table): void {
            $table->boolean('auto_convert_quote')->default(true);
        });

        // we need to create the last status to resolve a foreign key constraint
        if (DB::table('invoice_statuses')->count() == 5) {
            DB::table('invoice_statuses')->insert([
                'id'   => 6,
                'name' => 'Paid',
            ]);
        }

        DB::table('invoices')
            ->whereIn('invoice_status_id', [4, 5])
            ->increment('invoice_status_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table): void {
            $table->dropColumn('auto_convert_quote');
        });

        DB::table('invoices')
            ->whereIn('invoice_status_id', [5, 6])
            ->decrement('invoice_status_id');
    }
};
