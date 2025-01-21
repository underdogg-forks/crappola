<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDefaultRates extends Migration
{
    public function up(): void
    {
        // Add 'Four Months' frequency option
        if (DB::table('frequencies')->count() == 8) {
            DB::table('frequencies')->where('id', '=', 7)->update(['name' => 'Four months']);
            DB::table('frequencies')->where('id', '=', 8)->update(['name' => 'Six months']);
            DB::table('frequencies')->insert(['name' => 'Annually']);
            DB::statement('update invoices set frequency_id = frequency_id + 1 where frequency_id >= 7');
            DB::statement('update recurring_expenses set frequency_id = frequency_id + 1 where frequency_id >= 7');
            DB::statement('update companies set reset_counter_frequency_id = reset_counter_frequency_id + 1 where reset_counter_frequency_id >= 7');
        }
    }

    public function down(): void
    {
    }
}
