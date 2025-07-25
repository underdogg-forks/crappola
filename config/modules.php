<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Namespace
    |--------------------------------------------------------------------------
    |
    | Default module namespace.
    |
    */

    'namespace' => 'Modules',

    /*
    |--------------------------------------------------------------------------
    | Module Stubs
    |--------------------------------------------------------------------------
    |
    | Default module stubs.
    |
    */

    'stubs' => [
        'enabled' => true,
        'path'    => base_path() . '/app/Console/Commands/stubs',
        'files'   => [
            'start'           => 'start.php',
            'routes'          => 'Http/routes.php',
            'json'            => 'module.json',
            'views/master'    => 'Resources/views/layouts/master.blade.php',
            'scaffold/config' => 'Config/config.php',
            'composer'        => 'composer.json',
        ],
        'replacements' => [
            'start'           => ['LOWER_NAME'],
            'routes'          => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'json'            => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'views/master'    => ['STUDLY_NAME'],
            'scaffold/config' => ['STUDLY_NAME'],
            'composer'        => [
                'LOWER_NAME',
                'STUDLY_NAME',
                'VENDOR',
                'AUTHOR_NAME',
                'AUTHOR_EMAIL',
                'MODULE_NAMESPACE',
            ],
        ],
    ],
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Modules path
        |--------------------------------------------------------------------------
        |
        | This path used for save the generated module. This path also will added
        | automatically to list of scanned folders.
        |
        */

        'modules' => base_path('Modules'),
        /*
        |--------------------------------------------------------------------------
        | Modules assets path
        |--------------------------------------------------------------------------
        |
        | Here you may update the modules assets path.
        |
        */

        'assets' => public_path('modules'),
        /*
        |--------------------------------------------------------------------------
        | The migrations path
        |--------------------------------------------------------------------------
        |
        | Where you run 'module:publish-migration' command, where do you publish the
        | the migration files?
        |
        */

        'migration' => base_path('database/migrations'),
        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        |
        | Here you may update the modules generator path.
        |
        */

        'generator' => [
            'assets'         => 'Assets',
            'config'         => 'Config',
            'command'        => 'Console',
            'event'          => 'Events',
            'listener'       => 'Events/Handlers',
            'migration'      => 'Database/Migrations',
            'model'          => 'Models',
            'repository'     => 'Repositories',
            'seeder'         => 'Database/Seeders',
            'controller'     => 'Http/Controllers',
            'filter'         => 'Http/Middleware',
            'request'        => 'Http/Requests',
            'provider'       => 'Providers',
            'lang'           => 'Resources/lang/en',
            'views'          => 'Resources/views',
            'test'           => 'Tests',
            'jobs'           => 'Jobs',
            'emails'         => 'Emails',
            'notifications'  => 'Notifications',
            'datatable'      => 'Datatables',
            'policy'         => 'Policies',
            'presenter'      => 'Presenters',
            'api-controller' => 'Http/ApiControllers',
            'transformer'    => 'Transformers',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Scan Path
    |--------------------------------------------------------------------------
    |
    | Here you define which folder will be scanned. By default will scan vendor
    | directory. This is useful if you host the package in packagist website.
    |
    */

    'scan' => [
        'enabled' => false,
        'paths'   => [
            base_path('vendor/*/*'),
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | Here is the config for composer.json file, generated by this package
    |
    */

    'composer' => [
        'vendor' => 'invoiceninja',
        'author' => [
            'name'  => 'Hillel Coren',
            'email' => 'contact@invoiceninja.com',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Here is the config for setting up caching feature.
    |
    */
    'cache' => [
        'enabled'  => false,
        'key'      => 'laravel-modules',
        'lifetime' => 60,
    ],
    /*
    |--------------------------------------------------------------------------
    | Choose what laravel-modules will register as custom namespaces.
    | Setting one to false will require to register that part
    | in your own Service Provider class.
    |--------------------------------------------------------------------------
    */
    'register' => [
        'translations' => true,
    ],
    'relations' => [
        //  all dynamic relations registered from modules are added here
    ],
];
