<?php

use Illuminate\Database\Migrations\Migration;

class EncryptTokens extends Migration
{
    public function up()
    {
        $gateways = DB::table('account_gateways')
            ->get(['id', 'config']);
        foreach ($gateways as $gateway) {
            DB::table('account_gateways')
                ->where('id', $gateway->id)
                ->update(['config' => Crypt::encrypt($gateway->config)]);
        }
    }

    public function down()
    {
        $gateways = DB::table('account_gateways')
            ->get(['id', 'config']);
        foreach ($gateways as $gateway) {
            DB::table('account_gateways')
                ->where('id', $gateway->id)
                ->update(['config' => Crypt::decrypt($gateway->config)]);
        }
    }
}
