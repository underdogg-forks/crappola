<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class UpdateSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Running UpdateSeeder...');

        $this->call([
            CountriesSeeder::class,
            PaymentLibrariesSeeder::class,
            FontsSeeder::class,
            GatewayTypesSeeder::class,
            BanksSeeder::class,
            InvoiceStatusSeeder::class,
            PaymentStatusSeeder::class,
            CurrenciesSeeder::class,
            DateFormatsSeeder::class,
            InvoiceDesignsSeeder::class,
            ProposalTemplatesSeeder::class,
            PaymentTermsSeeder::class,
            PaymentTypesSeeder::class,
            LanguageSeeder::class,
            IndustrySeeder::class,
            FrequencySeeder::class,
            DbServerSeeder::class,
        ]);

        Cache::flush();
    }
}
