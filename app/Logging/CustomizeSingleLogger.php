<?php

namespace App\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class CustomizeSingleLogger
{
    /**
     * Customize the given logger instance.
     *
     * @param \Illuminate\Log\Logger $logger
     *
     * @return void
     */
    public function __invoke($logger): void
    {
        $logger->pushHandler(new StreamHandler(
            storage_path() . '/logs/laravel-info.log',
            Logger::INFO,
            false
        ));
        $logger->pushHandler(new StreamHandler(
            storage_path() . '/logs/laravel-warning.log',
            Logger::WARNING,
            false
        ));
        $logger->pushHandler(new StreamHandler(
            storage_path() . '/logs/laravel-error.log',
            Logger::ERROR,
            false
        ));
    }
}
