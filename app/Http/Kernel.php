<?php
namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        'App\Http\Middleware\StartupCheck',
        /*\Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'App\Http\Middleware\VerifyCsrfToken',
        'App\Http\Middleware\DuplicateSubmissionCheck',
        'App\Http\Middleware\QueryLogging',
        'App\Http\Middleware\StartupCheck',*/
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'auth' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'Illuminate\Cookie\Middleware\EncryptCookies',
            'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
            'App\Http\Middleware\VerifyCsrfToken',
            'App\Http\Middleware\DuplicateSubmissionCheck',
            'App\Http\Middleware\QueryLogging',
            'App\Http\Middleware\StartupCheck',
        ],

        'api' => [
            'throttle:60,1',
        ],
    ];


    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'lookup' => 'App\Http\Middleware\DatabaseLookup',
        'auth' => 'App\Http\Middleware\Authenticate',
        'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
        'permissions.required' => 'App\Http\Middleware\PermissionsRequired',
        'guest' => 'App\Http\Middleware\RedirectIfAuthenticated',
        'api' => 'App\Http\Middleware\ApiCheck',
        'cors' => '\Barryvdh\Cors\HandleCors',
    ];
}
