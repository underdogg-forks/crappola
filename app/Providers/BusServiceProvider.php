<?php

namespace App\Providers;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\ServiceProvider;

/**
 * Class BusServiceProvider.
 */
class BusServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param Dispatcher $dispatcher
     *
     * @return void
     */
    public function boot(Dispatcher $dispatcher): void
    {
        $dispatcher->mapUsing(fn ($command) => Dispatcher::simpleMapping(
            $command,
            'App\Commands',
            'App\Handlers\Commands'
        ));
    }

    /**
     * Register any application services.
     */
    public function register(): void {}
}
