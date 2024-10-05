<?php

namespace App\Providers;

use Form;
use Illuminate\Pagination\Paginator;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Module;
use Request;
use Utils;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Route::singularResourceParameters(false);
        Paginator::useBootstrapThree();

        // support selecting job database
        \Illuminate\Support\Facades\Queue::before(function (JobProcessing $event): void {
            $body = $event->job->getRawBody();
            preg_match('/db-ninja-[\d+]/', $body, $matches);
            if ($matches !== []) {
                config(['database.default' => $matches[0]]);
            }
        });

        Form::macro('image_data', function ($image, $contents = false): string {
            $contents = $contents ? $image : file_get_contents($image);

            return $contents ? 'data:image/jpeg;base64,' . base64_encode($contents) : '';
        });

        Form::macro('nav_link', function (string $url, $text): string {
            //$class = ( Request::is($url) || Request::is($url.'/*') || Request::is($url2.'/*') ) ? ' class="active"' : '';
            $class = (\Illuminate\Support\Facades\Request::is($url) || \Illuminate\Support\Facades\Request::is($url . '/*')) ? ' class="active"' : '';
            $title = trans("texts.{$text}") . Utils::getProLabel($text);

            return '<li' . $class . '><a href="' . \Illuminate\Support\Facades\URL::to($url) . '">' . $title . '</a></li>';
        });

        Form::macro('tab_link', function ($url, string $text, $active = false): string {
            $class = $active ? ' class="active"' : '';

            return '<li' . $class . '><a href="' . \Illuminate\Support\Facades\URL::to($url) . '" data-toggle="tab">' . $text . '</a></li>';
        });

        Form::macro('menu_link', function ($type): string {
            $types = $type . 's';
            $Type = ucfirst($type);
            $Types = ucfirst($types);
            $class = (\Illuminate\Support\Facades\Request::is($types) || \Illuminate\Support\Facades\Request::is('*' . $type . '*')) && ! \Illuminate\Support\Facades\Request::is('*settings*') ? ' active' : '';

            return '<li class="dropdown ' . $class . '">
                    <a href="' . \Illuminate\Support\Facades\URL::to($types) . '" class="dropdown-toggle">' . trans("texts.{$types}") . '</a>
                   </li>';
        });

        Form::macro('flatButton', fn ($label, $color): string => '<input type="button" value="' . trans("texts.{$label}") . '" style="background-color:' . $color . ';border:0 none;border-radius:5px;padding:12px 40px;margin:0 6px;cursor:hand;display:inline-block;font-size:14px;color:#fff;text-transform:none;font-weight:bold;"/>');

        Form::macro('emailViewButton', fn ($link = '#', $entityType = ENTITY_INVOICE) => view('partials.email_button')
            ->with([
                'link'  => $link,
                'field' => "view_{$entityType}",
                'color' => '#0b4d78',
            ])
            ->render());

        Form::macro('emailPaymentButton', fn ($link = '#', $label = 'pay_now') => view('partials.email_button')
            ->with([
                'link'  => $link,
                'field' => $label,
                'color' => '#36c157',
            ])
            ->render());

        Form::macro('breadcrumbs', function ($status = false): string {
            $str = '<ol class="breadcrumb">';

            // Get the breadcrumbs by exploding the current path.
            $basePath = Utils::basePath();
            $parts = explode('?', $_SERVER['REQUEST_URI'] ?? '');
            $path = $parts[0];

            if ($basePath != '/') {
                $path = str_replace($basePath, '', $path);
            }
            $crumbs = explode('/', $path);

            foreach ($crumbs as $key => $val) {
                if (is_numeric($val)) {
                    unset($crumbs[$key]);
                }
            }

            $crumbs = array_values($crumbs);
            $counter = count($crumbs);
            for ($i = 0; $i < $counter; $i++) {
                $crumb = trim($crumbs[$i]);
                if ( $crumb === '' || $crumb === '0') {
                    continue;
                }
                if ($crumb === 'company') {
                    return '';
                }

                $name = ! Utils::isNinjaProd() && $module = Module::find($crumb) ? mtrans($crumb) : trans("texts.{$crumb}");

                if ($i == count($crumbs) - 1) {
                    $str .= "<li class='active'>{$name}</li>";
                } else {
                    if (count($crumbs) > 2 && $crumbs[1] === 'proposals' && $crumb !== 'proposals') {
                        $crumb = 'proposals/' . $crumb;
                    }
                    $str .= '<li>' . link_to($crumb, $name) . '</li>';
                }
            }

            if ($status) {
                $str .= $status;
            }

            return $str . '</ol>';
        });

        Form::macro('human_filesize', function ($bytes, $decimals = 1): string {
            $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            $factor = floor((mb_strlen($bytes) - 1) / 3);
            if ($factor == 0) {
                $decimals = 0;
            }// There aren't fractional bytes

            return sprintf("%.{$decimals}f", $bytes / 1024 ** $factor) . ' ' . @$size[$factor];
        });

        \Illuminate\Support\Facades\Validator::extend('positive', fn ($attribute, $value, $parameters): bool => Utils::parseFloat($value) >= 0);

        \Illuminate\Support\Facades\Validator::extend('has_credit', function ($attribute, $value, $parameters): bool {
            $publicClientId = $parameters[0];
            $amount = $parameters[1];

            $client = \App\Models\Client::scope($publicClientId)->firstOrFail();
            $credit = $client->getTotalCredit();

            return $credit >= $amount;
        });

        // check that the time log elements don't overlap
        \Illuminate\Support\Facades\Validator::extend('time_log', function ($attribute, $value, $parameters): bool {
            $lastTime = 0;
            $value = json_decode($value);
            array_multisort($value);
            foreach ($value as $timeLog) {
                [$startTime, $endTime] = $timeLog;
                if ( ! $endTime) {
                    continue;
                }
                if ($startTime < $lastTime || $startTime > $endTime) {
                    return false;
                }
                if ($endTime < min($startTime, $lastTime)) {
                    return false;
                }
                $lastTime = max($lastTime, $endTime);
            }

            return true;
        });

        \Illuminate\Support\Facades\Validator::extend('has_counter', function ($attribute, $value, $parameters): bool {
            if ( ! $value) {
                return true;
            }

            if (mb_strstr($value, '{$counter}') !== false) {
                return true;
            }

            return (mb_strstr($value, '{$idNumber}') !== false || mb_strstr($value, '{$clientIdNumber}') != false) && (mb_strstr($value, '{$clientCounter}'));
        });

        \Illuminate\Support\Facades\Validator::extend('valid_invoice_items', function ($attribute, $value, $parameters): bool {
            $total = 0;
            foreach ($value as $item) {
                $qty = empty($item['qty']) ? 1 : Utils::parseFloat($item['qty']);
                $cost = empty($item['cost']) ? 1 : Utils::parseFloat($item['cost']);
                $total += $qty * $cost;
            }

            return $total <= MAX_INVOICE_AMOUNT;
        });

        \Illuminate\Support\Facades\Validator::extend('valid_subdomain', fn ($attribute, $value, $parameters): bool => ! in_array($value, ['www', 'app', 'mail', 'admin', 'blog', 'user', 'contact', 'payment', 'payments', 'billing', 'invoice', 'business', 'owner', 'info', 'ninja', 'docs', 'doc', 'documents', 'download']));
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            'Illuminate\Contracts\Auth\Registrar',
            'App\Services\Registrar'
        );
    }
}
