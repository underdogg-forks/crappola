<?php
use App\Models\Currency;

class CurrenciesSeeder extends Seeder
{
    public function run()
    {
        Eloquent::unguard();
        // http://www.localeplanet.com/icu/currency.html
        $currencies = [
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$', 'precision' => '2', 'thousand_separator' => ',', 'decimal_separator' => '.'],
            ['name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£', 'precision' => '2', 'thousand_separator' => ',', 'decimal_separator' => '.'],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'precision' => '2', 'thousand_separator' => '.', 'decimal_separator' => ','],
        ];
        foreach ($currencies as $currency) {
            $record = Currency::whereCode($currency['code'])->first();
            if ($record) {
                $record->name = $currency['name'];
                $record->symbol = $currency['symbol'];
                $record->thousand_separator = $currency['thousand_separator'];
                $record->decimal_separator = $currency['decimal_separator'];
                if (isset($currency['swap_currency_symbol'])) {
                    $record->swap_currency_symbol = $currency['swap_currency_symbol'];
                }
                $record->save();
            } else {
                Currency::create($currency);
            }
        }
    }
}
