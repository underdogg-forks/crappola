<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Utils;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SendRecurringInvoices::class,
        \App\Console\Commands\RemoveOrphanedDocuments::class,
        \App\Console\Commands\ResetData::class,
        \App\Console\Commands\CheckData::class,
        \App\Console\Commands\PruneData::class,
        \App\Console\Commands\CreateTestData::class,
        \App\Console\Commands\CreateLuisData::class,
        \App\Console\Commands\MobileLocalization::class,
        \App\Console\Commands\SendRenewalInvoices::class,
        \App\Console\Commands\ChargeRenewalInvoices::class,
        \App\Console\Commands\SendReminders::class,
        \App\Console\Commands\TestOFX::class,
        \App\Console\Commands\MakeModule::class,
        \App\Console\Commands\MakeClass::class,
        \App\Console\Commands\InitLookup::class,
        \App\Console\Commands\CalculatePayouts::class,
        \App\Console\Commands\UpdateKey::class,
        \App\Console\Commands\ExportMigrations::class,
        \App\Console\Commands\SyncAccounts::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $logFile = storage_path() . '/logs/cron.log';

        $schedule
            ->command('ninja:send-invoices --force')
            ->sendOutputTo($logFile)
            ->withoutOverlapping()
            ->hourly();

        $schedule
            ->command('ninja:send-reminders --force')
            ->sendOutputTo($logFile)
            ->daily();

        if (Utils::isNinjaProd()) {
            $schedule
                ->command('ninja:sync-v5')
                ->withoutOverlapping()
                ->daily();

            // $schedule
            //     ->command('ninja:force-migrate-v5')
            //     ->everyMinute()
            //     ->withoutOverlapping();
        }
    }
}
