<?php

namespace App\Ninja\Repositories;

use App\Models\AccountGatewayToken;
use App\Models\PaymentMethod;

class CustomerRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\AccountGatewayToken';
    }

    public function all()
    {
        return AccountGatewayToken::whereCompanyPlanId(auth()->user()->company_id)
            ->with(['contact'])
            ->get();
    }

    public function save($data)
    {
        $company = auth()->user()->company;

        $customer = new AccountGatewayToken();
        $customer->company_id = $company->id;
        $customer->fill($data);
        $customer->save();

        $paymentMethod = PaymentMethod::createNew();
        $paymentMethod->account_gateway_token_id = $customer->id;
        $paymentMethod->fill($data['payment_method']);
        $paymentMethod->save();

        $customer->default_payment_method_id = $paymentMethod->id;
        $customer->save();

        return $customer;
    }
}
