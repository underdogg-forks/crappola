<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddInvoiceNumberSettings extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});

        // set initial counter value for accounts with invoices
        $accounts = DB::table('accounts')->pluck('id');

        foreach ($accounts as $accountId) {
            $invoiceNumbers = DB::table('invoices')->where('account_id', $accountId)->pluck('invoice_number');
            $max            = 0;

            foreach ($invoiceNumbers as $invoiceNumber) {
                $number = (int) (preg_replace('/[^0-9]/', '', $invoiceNumber));
                $max    = max($max, $number);
            }

            DB::table('accounts')->where('id', $accountId)->update(['invoice_number_counter' => ++$max]);
        }
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('invoice_number_prefix');
            $table->dropColumn('invoice_number_counter');

            $table->dropColumn('quote_number_prefix');
            $table->dropColumn('quote_number_counter');

            $table->dropColumn('share_counter');
        });
    }
}
