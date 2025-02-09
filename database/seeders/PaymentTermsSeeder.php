<?php

namespace Database\Seeders;

use App\Models\PaymentTerm;
use Illuminate\Database\Seeder;

class PaymentTermsSeeder extends Seeder
{
    public function run(): void
    {
        Eloquent::unguard();

        $paymentTerms = [
            ['num_days' => -1, 'name' => 'Net 0'],
        ];

        foreach ($paymentTerms as $paymentTerm) {
            if ( ! DB::table('payment_terms')->where('name', '=', $paymentTerm['name'])->first()) {
                PaymentTerm::create($paymentTerm);
            }
        }
    }
}
