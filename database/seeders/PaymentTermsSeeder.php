<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\PaymentTerm;

class PaymentTermsSeeder extends Seeder
{
    public function run(): void
    {
        

        $paymentTerms = [
            ['num_days' => -1, 'name' => 'Net 0'],
        ];

        foreach ($paymentTerms as $paymentTerm) {
            if (! DB::table('payment_terms')->where('name', '=', $paymentTerm['name'])->first()) {
                PaymentTerm::create($paymentTerm);
            }
        }
    }
}
