<?php

use Illuminate\Support\Facades\Facade;

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Invoice Ninja'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */
    'debug' => (bool) env('APP_DEBUG', true),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */
    'env' => env('APP_ENV', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', ''),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', 'SomeRandomStringSomeRandomString'),

    'cipher' => env('APP_CIPHER', 'AES-256-CBC'),

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [
        // Laravel Framework Service Providers...
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        // Additional Providers
        'Bootstrapper\BootstrapperL5ServiceProvider',
        'Former\FormerServiceProvider',
        'Barryvdh\Debugbar\ServiceProvider',
        \Intervention\Image\Laravel\ServiceProvider::class,
        'Webpatser\Countries\CountriesServiceProvider',
        'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',
        'Laravel\Socialite\SocialiteServiceProvider',
        'Jlapp\Swaggervel\SwaggervelServiceProvider',
        'Maatwebsite\Excel\ExcelServiceProvider',
        Codedge\Updater\UpdaterServiceProvider::class,
        Nwidart\Modules\LaravelModulesServiceProvider::class,
        Fruitcake\Cors\CorsServiceProvider::class,
        PragmaRX\Google2FALaravel\ServiceProvider::class,
        'Chumper\Datatable\DatatableServiceProvider',
        Laravel\Tinker\TinkerServiceProvider::class,

        // Application Service Providers...
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        'App\Providers\ComposerServiceProvider',
        'App\Providers\ConfigServiceProvider',
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        'App'         => 'Illuminate\Support\Facades\App',
        'Artisan'     => 'Illuminate\Support\Facades\Artisan',
        'Auth'        => 'Illuminate\Support\Facades\Auth',
        'Blade'       => 'Illuminate\Support\Facades\Blade',
        'Cache'       => 'Illuminate\Support\Facades\Cache',
        'ClassLoader' => 'Illuminate\Support\ClassLoader',
        'Config'      => 'Illuminate\Support\Facades\Config',
        'Controller'  => 'Illuminate\Routing\Controller',
        'Cookie'      => 'Illuminate\Support\Facades\Cookie',
        'Crypt'       => 'Illuminate\Support\Facades\Crypt',
        'DB'          => 'Illuminate\Support\Facades\DB',
        'Eloquent'    => 'Illuminate\Database\Eloquent\Model',
        'Event'       => 'Illuminate\Support\Facades\Event',
        'File'        => 'Illuminate\Support\Facades\File',
        'Gate'        => 'Illuminate\Support\Facades\Gate',
        'Hash'        => 'Illuminate\Support\Facades\Hash',
        'Input'       => 'Illuminate\Support\Facades\Input',
        'Lang'        => 'Illuminate\Support\Facades\Lang',
        'Log'         => 'Illuminate\Support\Facades\Log',
        'Mail'        => 'Illuminate\Support\Facades\Mail',
        'Password'    => 'Illuminate\Support\Facades\Password',
        'Queue'       => 'Illuminate\Support\Facades\Queue',
        'Redirect'    => 'Illuminate\Support\Facades\Redirect',
        'Redis'       => 'Illuminate\Support\Facades\Redis',
        'Request'     => 'Illuminate\Support\Facades\Request',
        'Response'    => 'Illuminate\Support\Facades\Response',
        'Route'       => 'Illuminate\Support\Facades\Route',
        'Schema'      => 'Illuminate\Support\Facades\Schema',
        'Seeder'      => 'Illuminate\Database\Seeder',
        'Session'     => 'Illuminate\Support\Facades\Session',
        'Storage'     => 'Illuminate\Support\Facades\Storage',
        'Str'         => 'Illuminate\Support\Str',
        'URL'         => 'Illuminate\Support\Facades\URL',
        'Validator'   => 'Illuminate\Support\Facades\Validator',
        'View'        => 'Illuminate\Support\Facades\View',

        // Added Class Aliases
        'Form'           => 'Collective\Html\FormFacade',
        'HTML'           => 'Collective\Html\HtmlFacade',
        'SSH'            => 'Illuminate\Support\Facades\SSH',
        'Alert'          => 'Bootstrapper\Facades\Alert',
        'Badge'          => 'Bootstrapper\Facades\Badge',
        'Breadcrumb'     => 'Bootstrapper\Facades\Breadcrumb',
        'Button'         => 'Bootstrapper\Facades\Button',
        'ButtonGroup'    => 'Bootstrapper\Facades\ButtonGroup',
        'ButtonToolbar'  => 'Bootstrapper\Facades\ButtonToolbar',
        'Carousel'       => 'Bootstrapper\Facades\Carousel',
        'DropdownButton' => 'Bootstrapper\Facades\DropdownButton',
        'Helpers'        => 'Bootstrapper\Facades\Helpers',
        'Icon'           => 'Bootstrapper\Facades\Icon',
        'Label'          => 'Bootstrapper\Facades\Label',
        'MediaObject'    => 'Bootstrapper\Facades\MediaObject',
        'Navbar'         => 'Bootstrapper\Facades\Navbar',
        'Navigation'     => 'Bootstrapper\Facades\Navigation',
        'Paginator'      => 'Bootstrapper\Facades\Paginator',
        'Progress'       => 'Bootstrapper\Facades\Progress',
        'Tabbable'       => 'Bootstrapper\Facades\Tabbable',
        'Table'          => 'Bootstrapper\Facades\Table',
        'Thumbnail'      => 'Bootstrapper\Facades\Thumbnail',
        'Typeahead'      => 'Bootstrapper\Facades\Typeahead',
        'Typography'     => 'Bootstrapper\Facades\Typography',
        'Former'         => 'Former\Facades\Former',
        'Omnipay'        => 'Omnipay\Omnipay',
        'CreditCard'     => 'Omnipay\Common\CreditCard',
        'Image'          => \Intervention\Image\Laravel\Facades\Image::class,
        'Countries'      => 'Webpatser\Countries\CountriesFacade',
        'Carbon'         => 'Carbon\Carbon',
        'Rocketeer'      => 'Rocketeer\Facades\Rocketeer',
        'Socialite'      => 'Laravel\Socialite\Facades\Socialite',
        'Excel'          => 'Maatwebsite\Excel\Facades\Excel',
        'Datatable'      => 'Chumper\Datatable\Facades\DatatableFacade',
        'Updater'        => Codedge\Updater\UpdaterFacade::class,
        'Module'         => Nwidart\Modules\Facades\Module::class,

        'Utils'     => App\Libraries\Utils::class,
        'DateUtils' => App\Libraries\DateUtils::class,
        'HTMLUtils' => App\Libraries\HTMLUtils::class,
        'CurlUtils' => App\Libraries\CurlUtils::class,
        'Domain'    => App\Constants\Domain::class,
        'Google2FA' => PragmaRX\Google2FALaravel\Facade::class,
    ])->toArray(),
];
