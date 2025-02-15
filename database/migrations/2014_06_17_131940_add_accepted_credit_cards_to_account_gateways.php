<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddAcceptedCreditCardsToAccountGateways extends Migration
{
    public function up()
    {
        Schema::table('account_gateways', function ($table) {});
    }

    public function down()
    {
        Schema::table('account_gateways', function ($table) {
            $table->dropColumn('accepted_credit_cards');
        });
    }
}
