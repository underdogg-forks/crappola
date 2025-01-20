<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SendRecurringInvoices',
        'App\Console\Commands\RemoveOrphanedDocuments',
        'App\Console\Commands\ResetData',
        'App\Console\Commands\CheckData',
        'App\Console\Commands\PruneData',
        'App\Console\Commands\CreateTestData',
        'App\Console\Commands\CreateLuisData',
        'App\Console\Commands\SendRenewalInvoices',
        'App\Console\Commands\ChargeRenewalInvoices',
        'App\Console\Commands\SendReminders',
        'App\Console\Commands\TestOFX',
        'App\Console\Commands\InitLookup',
        'App\Console\Commands\CalculatePayouts',
        'App\Console\Commands\UpdateKey',
        'App\Console\Commands\MobileLocalization',
        'App\Console\Commands\SendOverdueTickets',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $logFile = storage_path() . '/logs/cron.log';

        $schedule
            ->command('ninja:send-invoices')
            ->sendOutputTo($logFile)
            ->withoutOverlapping()
            ->hourly();

        $schedule
            ->command('ninja:send-reminders')
            ->sendOutputTo($logFile)
            ->daily();
    }
}
