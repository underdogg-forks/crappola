<?php

namespace App\Console;

use App\Console\Commands\CalculatePayouts;
use App\Console\Commands\ChargeRenewalInvoices;
use App\Console\Commands\CheckData;
use App\Console\Commands\CreateLuisData;
use App\Console\Commands\CreateTestData;
use App\Console\Commands\InitLookup;
use App\Console\Commands\MobileLocalization;
use App\Console\Commands\PruneData;
use App\Console\Commands\RemoveOrphanedDocuments;
use App\Console\Commands\ResetData;
use App\Console\Commands\SendOverdueTickets;
use App\Console\Commands\SendRecurringInvoices;
use App\Console\Commands\SendReminders;
use App\Console\Commands\SendRenewalInvoices;
use App\Console\Commands\TestOFX;
use App\Console\Commands\UpdateKey;
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
        SendRecurringInvoices::class,
        RemoveOrphanedDocuments::class,
        ResetData::class,
        CheckData::class,
        PruneData::class,
        CreateTestData::class,
        CreateLuisData::class,
        SendRenewalInvoices::class,
        ChargeRenewalInvoices::class,
        SendReminders::class,
        TestOFX::class,
        InitLookup::class,
        CalculatePayouts::class,
        UpdateKey::class,
        MobileLocalization::class,
        SendOverdueTickets::class,
    ];

    /**
     * Define the application's command schedule.
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
