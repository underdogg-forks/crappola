<?php

namespace App\Console;

use App\Console\Commands\CalculatePayouts;
use App\Console\Commands\ChargeRenewalInvoices;
use App\Console\Commands\CheckData;
use App\Console\Commands\CreateLuisData;
use App\Console\Commands\CreateTestData;
use App\Console\Commands\ExportMigrations;
use App\Console\Commands\InitLookup;
use App\Console\Commands\MakeClass;
use App\Console\Commands\MakeModule;
use App\Console\Commands\MobileLocalization;
use App\Console\Commands\PruneData;
use App\Console\Commands\RemoveOrphanedDocuments;
use App\Console\Commands\ResetData;
use App\Console\Commands\SendRecurringInvoices;
use App\Console\Commands\SendReminders;
use App\Console\Commands\SendRenewalInvoices;
use App\Console\Commands\SyncAccounts;
use App\Console\Commands\TestOFX;
use App\Console\Commands\UpdateKey;
use App\Libraries\Utils;
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
        MobileLocalization::class,
        SendRenewalInvoices::class,
        ChargeRenewalInvoices::class,
        SendReminders::class,
        TestOFX::class,
        MakeModule::class,
        MakeClass::class,
        InitLookup::class,
        CalculatePayouts::class,
        UpdateKey::class,
        ExportMigrations::class,
        SyncAccounts::class,
    ];

    /**
     * Define the application's command schedule.
     *
     *
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
