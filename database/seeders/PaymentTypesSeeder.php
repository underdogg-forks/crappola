<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PaymentTypesSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $paymentTypes = [
            ['name' => 'Apply Credit'],
            ['name' => 'Bank Transfer', 'gateway_type_id' => GATEWAY_TYPE_BANK_TRANSFER],
            ['name' => 'Cash'],
            ['name' => 'Debit', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'ACH', 'gateway_type_id' => GATEWAY_TYPE_BANK_TRANSFER],
            ['name' => 'Visa Card', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'MasterCard', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'American Express', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'Discover Card', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'Diners Card', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'EuroCard', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'Nova', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'Credit Card Other', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'PayPal', 'gateway_type_id' => GATEWAY_TYPE_PAYPAL],
            ['name' => 'Google Wallet'],
            ['name' => 'Check'],
            ['name' => 'Carte Blanche', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'UnionPay', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'JCB', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'Laser', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'Maestro', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'Solo', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'Switch', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'iZettle', 'gateway_type_id' => GATEWAY_TYPE_CREDIT_CARD],
            ['name' => 'Swish', 'gateway_type_id' => GATEWAY_TYPE_BANK_TRANSFER],
            ['name' => 'Venmo'],
            ['name' => 'Money Order'],
            ['name' => 'Alipay', 'gateway_type_id' => GATEWAY_TYPE_ALIPAY],
            ['name' => 'Sofort', 'gateway_type_id' => GATEWAY_TYPE_SOFORT],
            ['name' => 'SEPA', 'gateway_type_id' => GATEWAY_TYPE_SEPA],
            ['name' => 'GoCardless', 'gateway_type_id' => GATEWAY_TYPE_GOCARDLESS],
            ['name' => 'Bitcoin', 'gateway_type_id' => GATEWAY_TYPE_BITCOIN],
            ['name' => 'Zelle'],
        ];

        foreach ($paymentTypes as $paymentType) {
            $gatewayType = $paymentType['gateway_type_id'] ?? null;

            PaymentType::updateOrCreate(
                ['name' => $paymentType['name'], 'gateway_type_id' => $gatewayType],
                $paymentType
            );
        }
    }
}
