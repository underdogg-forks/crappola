<?php

namespace Database\Seeders;

use App\Models\Timezone;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $this->command->info('Running DatabaseSeeder');

        $this->call(ConstantsSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(PaymentLibrariesSeeder::class);
        $this->call(FontsSeeder::class);
        $this->call(GatewayTypesSeeder::class);
        $this->call(BanksSeeder::class);
        $this->call(InvoiceStatusSeeder::class);
        $this->call(ProposalTemplatesSeeder::class);
        $this->call(PaymentStatusSeeder::class);
        $this->call(CurrenciesSeeder::class);
        $this->call(DateFormatsSeeder::class);
        $this->call(InvoiceDesignsSeeder::class);
        $this->call(PaymentTermsSeeder::class);
        $this->call(PaymentTypesSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(IndustrySeeder::class);
        $this->call(FrequencySeeder::class);

        /*if (Timezone::count()) {
            $this->command->info('Skipping: already run::class);
            return;
        }*/
        //$this->call(UserTableSeeder::class);
    }
}
