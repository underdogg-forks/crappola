<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDefaultQuoteTerms extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {});

        $accounts = DB::table('accounts')
            ->orderBy('id')
            ->get(['id', 'invoice_terms']);

        foreach ($accounts as $account) {
            DB::table('accounts')
                ->where('id', $account->id)
                ->update(['quote_terms' => $account->invoice_terms]);
        }
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('quote_terms');
        });
    }
}
