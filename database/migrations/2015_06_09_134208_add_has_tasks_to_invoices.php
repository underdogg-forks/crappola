<?php

use Illuminate\Database\Migrations\Migration;

class AddHasTasksToInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('invoices', function ($table): void {
            $table->boolean('has_tasks')->default(false);
        });

        $invoices = DB::table('invoices')
            ->join('tasks', 'tasks.invoice_id', '=', 'invoices.id')
            ->selectRaw('DISTINCT invoices.id')
            ->get();

        foreach ($invoices as $invoice) {
            DB::table('invoices')
                ->where('id', $invoice->id)
                ->update(['has_tasks' => true]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invoices', function ($table): void {
            $table->dropColumn('has_tasks');
        });
    }
}
