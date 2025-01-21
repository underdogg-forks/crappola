<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddHasTasksToInvoices extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function ($table): void {
            $table->boolean('has_tasks')->after('invoice_footer')->default(false);
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

    public function down(): void
    {
        Schema::table('invoices', function ($table): void {
            $table->dropColumn('has_tasks');
        });
    }
}
