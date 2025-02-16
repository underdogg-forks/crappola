<?php

namespace Database\Seeders;

use App\Models\Timezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Running DatabaseSeeder');

        if (Timezone::count()) {
            $this->command->info('Skipping: already run');

            return;
        }

        Model::unguard();

        $this->call([
            CountriesSeeder::class,
            CountriesSeeder::class,
            PaymentLibrariesSeeder::class,
            FontsSeeder::class,
            GatewayTypesSeeder::class,
            BanksSeeder::class,
            InvoiceStatusSeeder::class,
            ProposalTemplatesSeeder::class,
            PaymentStatusSeeder::class,
            CurrenciesSeeder::class,
            DateFormatsSeeder::class,
            InvoiceDesignsSeeder::class,
            PaymentTermsSeeder::class,
            PaymentTypesSeeder::class,
            LanguageSeeder::class,
            IndustrySeeder::class,
            FrequencySeeder::class,
            DbServerSeeder::class,
        ]);
    }
}
