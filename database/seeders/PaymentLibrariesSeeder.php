<?php

namespace Database\Seeders;

use App\Models\Gateway;
use Illuminate\Database\Seeder;

class PaymentLibrariesSeeder extends Seeder
{
    public function run()
    {
        Eloquent::unguard();
        $gateways = [
            ['name' => 'Mollie', 'provider' => 'Mollie', 'is_offsite' => true, 'sort_order' => 7],
            ['name' => 'MultiSafepay', 'provider' => 'MultiSafepay'],
            ['name' => 'Pin', 'provider' => 'Pin'],
            ['name' => 'Sisow', 'provider' => 'Sisow'],
            ['name' => 'Custom', 'provider' => 'Custom', 'is_offsite' => true, 'sort_order' => 8],
        ];
        foreach ($gateways as $gateway) {
            $record = Gateway::where('name', '=', $gateway['name'])->first();
            if ($record) {
                $record->fill($gateway);
                $record->save();
            } else {
                Gateway::create($gateway);
            }
        }
    }
}
