<?php

namespace App\Ninja\Repositories;

class AccountGatewayRepository extends BaseRepository
{
    public function getClassName(): string
    {
        return \App\Models\AccountGateway::class;
    }

    public function find($accountId)
    {
        $query = \Illuminate\Support\Facades\DB::table('account_gateways')
            ->join('gateways', 'gateways.id', '=', 'account_gateways.gateway_id')
            ->join('accounts', 'accounts.id', '=', 'account_gateways.account_id')
            ->where('account_gateways.account_id', '=', $accountId)
            ->whereNull('account_gateways.deleted_at');

        return $query->select(
            'account_gateways.id',
            'account_gateways.public_id',
            'gateways.name',
            'gateways.name as gateway',
            'account_gateways.deleted_at',
            'account_gateways.gateway_id',
            'accounts.gateway_fee_enabled'
        );
    }
}
