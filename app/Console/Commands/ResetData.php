<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Libraries\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ResetData.
 */
class ResetData extends Command
{
    /**
     * @var string
     */
    protected $name = 'ninja:reset-data';

    /**
     * @var string
     */
    protected $description = 'Reset data';

    public function handle(): void
    {
        $this->info(Carbon::now()->format('r') . ' Running ResetData...');

        if ( ! Utils::isNinjaDev()) {
            return;
        }

        if ($database = $this->option('database')) {
            config(['database.default' => $database]);
        }

        Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    protected function getOptions(): array
    {
        return [
            ['fix', null, InputOption::VALUE_OPTIONAL, 'Fix data', null],
            ['client_id', null, InputOption::VALUE_OPTIONAL, 'Client id', null],
            ['database', null, InputOption::VALUE_OPTIONAL, 'Database', null],
        ];
    }
}
