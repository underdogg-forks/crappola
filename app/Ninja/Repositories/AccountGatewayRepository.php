<?php

namespace App\Ninja\Repositories;

use Illuminate\Support\Facades\DB;

class AccountGatewayRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\AccountGateway';
    }

    public function find($companyId)
    {
        $query = DB::table('account_gateways')
            ->join('gateways', 'gateways.id', '=', 'account_gateways.gateway_id')
            ->join('companies', 'companies.id', '=', 'account_gateways.company_id')
            ->where('account_gateways.company_id', '=', $companyId)
            ->whereNull('account_gateways.deleted_at');

        return $query->select(
            'account_gateways.id',
            'account_gateways.public_id',
            'gateways.name',
            'gateways.name as gateway',
            'account_gateways.deleted_at',
            'account_gateways.gateway_id',
            'companies.gateway_fee_enabled'
        );
    }
}
