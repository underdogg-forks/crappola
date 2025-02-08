<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypesSeeder extends Seeder
{
    public function run()
    {
        Eloquent::unguard();
        $paymentTypes = [
            ['name' => 'Apply Credit'],
            ['name' => 'Bank Transfer', 'gateway_type_id' => GATEWAY_TYPE_BANK_TRANSFER],
            ['name' => 'Cash'],
            ['name' => 'Debit', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
        ];
        foreach ($paymentTypes as $paymentType) {
            $record = PaymentType::where('name', '=', $paymentType['name'])->first();
            if ($record) {
                $record->name = $paymentType['name'];
                $record->gateway_type_id = !empty($paymentType['gateway_type_id']) ? $paymentType['gateway_type_id'] : null;
                $record->save();
            } else {
                PaymentType::create($paymentType);
            }
        }
    }
}
