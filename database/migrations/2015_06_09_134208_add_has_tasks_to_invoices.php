<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddHasTasksToInvoices extends Migration
{
    public function up()
    {
        Schema::table('invoices', function ($table) {});

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

    public function down()
    {
        Schema::table('invoices', function ($table) {
            $table->dropColumn('has_tasks');
        });
    }
}
