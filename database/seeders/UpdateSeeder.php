<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class UpdateSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Running UpdateSeeder...');

        //$this->call(CountriesSeeder::class);
        //$this->call(PaymentLibrariesSeeder::class);
        //$this->call(FontsSeeder::class);
        //$this->call(GatewayTypesSeeder::class);
        //$this->call(BanksSeeder::class);
        $this->call(InvoiceStatusSeeder::class);
        $this->call(PaymentStatusSeeder::class);
        $this->call(CurrenciesSeeder::class);
        $this->call(DateFormatsSeeder::class);
        $this->call(InvoiceDesignsSeeder::class);
        $this->call(ProposalTemplatesSeeder::class);
        $this->call(PaymentTermsSeeder::class);
        $this->call(PaymentTypesSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(IndustrySeeder::class);
        $this->call(FrequencySeeder::class);
        $this->call(DbServerSeeder::class);

        Cache::flush();
    }
}
