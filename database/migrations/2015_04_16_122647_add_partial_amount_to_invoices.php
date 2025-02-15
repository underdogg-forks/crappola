<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddPartialAmountToInvoices extends Migration
{
    public function up()
    {
        Schema::table('invoices', function ($table) {});

        Schema::table('accounts', function ($table) {});
    }

    public function down()
    {
        Schema::table('invoices', function ($table) {
            $table->dropColumn('partial');
        });

        Schema::table('accounts', function ($table) {
            if (Schema::hasColumn('accounts', 'utf8_invoices')) {
                $table->dropColumn('utf8_invoices');
            }
            if (Schema::hasColumn('accounts', 'auto_wrap')) {
                $table->dropColumn('auto_wrap');
            }
            $table->dropColumn('subdomain');
        });
    }
}
