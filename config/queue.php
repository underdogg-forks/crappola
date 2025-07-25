<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

    'default' => env('QUEUE_DRIVER', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'connection' => env('QUEUE_DATABASE', 'mysql'),
            'driver'     => 'database',
            'table'      => 'jobs',
            'queue'      => 'default',
            'expire'     => 60,
        ],

        'beanstalkd' => [
            'driver'       => 'beanstalkd',
            'host'         => 'localhost',
            'queue'        => 'default',
            'ttr'          => 60,
            'block_for'    => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key'    => 'your-public-key',
            'secret' => 'your-secret-key',
            'queue'  => 'your-queue-url',
            'region' => 'us-east-1',
        ],

        'iron' => [
            'driver'  => 'iron',
            'host'    => env('QUEUE_HOST', 'mq-aws-us-east-1.iron.io'),
            'token'   => env('QUEUE_TOKEN'),
            'project' => env('QUEUE_PROJECT'),
            'queue'   => env('QUEUE_NAME'),
            'encrypt' => true,
        ],

        'redis' => [
            'driver'       => 'redis',
            'queue'        => env('REDIS_QUEUE', 'default'),
            'expire'       => 60,
            'retry_after'  => 90,
            'block_for'    => null,
            'after_commit' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('QUEUE_DATABASE', 'mysql'),
        'table'    => 'failed_jobs',
    ],
];
