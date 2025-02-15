<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSwapCurrencySymbolToCurrency extends Migration
{
    public function up()
    {
        Schema::table('currencies', function (Blueprint $table) {});

        Schema::table('expenses', function (Blueprint $table) {});

        Schema::table('account_gateways', function (Blueprint $table) {});
    }

    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn('swap_currency_symbol');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('tax_name1');
            $table->dropColumn('tax_rate1');
            $table->dropColumn('tax_name2');
            $table->dropColumn('tax_rate2');
        });

        Schema::table('account_gateways', function (Blueprint $table) {
            $table->dropColumn('require_cvv');
        });
    }
}
