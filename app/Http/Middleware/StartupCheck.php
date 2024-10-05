<?php

namespace App\Http\Middleware;

use App;
use App\Events\UserLoggedIn;
use App\Libraries\CurlUtils;
use App\Models\Language;
use Auth;
use Cache;
use Closure;
use Event;
use Illuminate\Http\Request;
use Redirect;
use Schema;
use Session;
use Utils;

/**
 * Class StartupCheck.
 */
class StartupCheck
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Set up trusted X-Forwarded-Proto proxies
        // TRUSTED_PROXIES accepts a comma delimited list of subnets
        // ie, TRUSTED_PROXIES='10.0.0.0/8,172.16.0.0/12,192.168.0.0/16'
        // set TRUSTED_PROXIES=* if you want to trust every proxy.
        if (isset($_ENV['TRUSTED_PROXIES'])) {
            if (env('TRUSTED_PROXIES') == '*') {
                $request->setTrustedProxies(['127.0.0.1', $request->server->get('REMOTE_ADDR')], Request::HEADER_X_FORWARDED_ALL);
            } else {
                $request->setTrustedProxies(array_map('trim', explode(',', env('TRUSTED_PROXIES'))), Request::HEADER_X_FORWARDED_ALL);
            }
        }

        // Ensure all request are over HTTPS in production
        if (Utils::requireHTTPS() && ! $request->secure()) {
            return \Illuminate\Support\Facades\Redirect::secure($request->path());
        }

        // If the database doens't yet exist we'll skip the rest
        if ( ! Utils::isNinja() && ! Utils::isDatabaseSetup()) {
            return $next($request);
        }

        // Check to prevent headless browsers from triggering activity
        if (Utils::isNinja() && ! $request->phantomjs && str_contains($request->header('User-Agent'), 'Headless')) {
            abort(403);
        }

        if (Utils::isSelfHost()) {
            // Check if config:cache may have been run
            if ( ! env('APP_URL')) {
                echo '<p>There appears to be a problem with your configuration, please check your .env file.</p>' .
                     "<p>If you've run 'php artisan config:cache' you will need to run 'php artisan config:clear'</p>.";
                exit;
            }

            // Check if a new version was installed
            $file = storage_path() . '/version.txt';
            $version = @file_get_contents($file);
            if ($version != NINJA_VERSION) {
                if (version_compare(phpversion(), '7.1.0', '<')) {
                    dd('Please update PHP to >= 7.1.0');
                }
                $handle = fopen($file, 'w');
                fwrite($handle, NINJA_VERSION);
                fclose($handle);

                return \Illuminate\Support\Facades\Redirect::to('/update');
            }
        }

        if (env('MULTI_DB_ENABLED')) {
            if ($server = session(SESSION_DB_SERVER)) {
                config(['database.default' => $server]);
            }
        }

        if (\Illuminate\Support\Facades\Auth::check()) {
            $company = \Illuminate\Support\Facades\Auth::user()->account->company;
            $count = \Illuminate\Support\Facades\Session::get(SESSION_COUNTER, 0);
            \Illuminate\Support\Facades\Session::put(SESSION_COUNTER, ++$count);

            if (Utils::isNinja()) {
                if (($coupon = request()->coupon) && ! $company->hasActivePlan()) {
                    if ($code = config('ninja.coupon_50_off')) {
                        if (hash_equals($coupon, $code)) {
                            $company->applyDiscount(.5);
                            $company->save();
                            \Illuminate\Support\Facades\Session::flash('message', trans('texts.applied_discount', ['discount' => 50]));
                        }
                    }
                    if ($code = config('ninja.coupon_75_off')) {
                        if (hash_equals($coupon, $code)) {
                            $company->applyDiscount(.75);
                            $company->save();
                            \Illuminate\Support\Facades\Session::flash('message', trans('texts.applied_discount', ['discount' => 75]));
                        }
                    }
                    if ($code = config('ninja.coupon_free_year')) {
                        if (hash_equals($coupon, $code)) {
                            $company->applyFreeYear();
                            $company->save();
                            \Illuminate\Support\Facades\Session::flash('message', trans('texts.applied_free_year'));
                        }
                    }
                }
            }

            // Check the application is up to date and for any news feed messages
            if (isset($_SERVER['REQUEST_URI']) && ! Utils::startsWith($_SERVER['REQUEST_URI'], '/news_feed') && ! \Illuminate\Support\Facades\Session::has('news_feed_id')) {
                $data = false;
                if (Utils::isNinja()) {
                    $data = Utils::getNewsFeedResponse();
                } else {
                    $file = @CurlUtils::get(NINJA_APP_URL . '/news_feed/' . Utils::getUserType() . '/' . NINJA_VERSION);
                    $data = @json_decode($file);
                }
                if ($data) {
                    if (version_compare(NINJA_VERSION, $data->version, '<')) {
                        $params = [
                            'user_version'   => NINJA_VERSION,
                            'latest_version' => $data->version,
                            'releases_link'  => link_to(RELEASES_URL, 'Invoice Ninja', ['target' => '_blank']),
                        ];
                        \Illuminate\Support\Facades\Session::put('news_feed_id', NEW_VERSION_AVAILABLE);
                        \Illuminate\Support\Facades\Session::flash('news_feed_message', trans('texts.new_version_available', $params));
                    } else {
                        \Illuminate\Support\Facades\Session::put('news_feed_id', $data->id);
                        if ($data->message && $data->id > \Illuminate\Support\Facades\Auth::user()->news_feed_id) {
                            \Illuminate\Support\Facades\Session::flash('news_feed_message', $data->message);
                        }
                    }
                } else {
                    \Illuminate\Support\Facades\Session::put('news_feed_id', true);
                }
            }
        }

        // Check if we're requesting to change the account's language
        if (\Illuminate\Support\Facades\Request::has('lang')) {
            $locale = \Illuminate\Support\Facades\Request::input('lang');
            \Illuminate\Support\Facades\App::setLocale($locale);
            session([SESSION_LOCALE => $locale]);

            if (\Illuminate\Support\Facades\Auth::check()) {
                if ($language = Language::whereLocale($locale)->first()) {
                    $account = \Illuminate\Support\Facades\Auth::user()->account;
                    $account->language_id = $language->id;
                    $account->save();
                }
            }
        } elseif (\Illuminate\Support\Facades\Auth::check()) {
            $locale = \Illuminate\Support\Facades\Auth::user()->account->language ? \Illuminate\Support\Facades\Auth::user()->account->language->locale : DEFAULT_LOCALE;
            \Illuminate\Support\Facades\App::setLocale($locale);
        } elseif (session(SESSION_LOCALE)) {
            \Illuminate\Support\Facades\App::setLocale(session(SESSION_LOCALE));
        }

        // Make sure the account/user localization settings are in the session
        if (\Illuminate\Support\Facades\Auth::check() && ! \Illuminate\Support\Facades\Session::has(SESSION_TIMEZONE)) {
            \Illuminate\Support\Facades\Event::dispatch(new UserLoggedIn());
        }

        // Check if the user is claiming a license (ie, additional invoices, white label, etc.)
        if ( ! Utils::isNinjaProd() && isset($_SERVER['REQUEST_URI'])) {
            $claimingLicense = Utils::startsWith($_SERVER['REQUEST_URI'], '/claim_license');
            if ( ! $claimingLicense && \Illuminate\Support\Facades\Request::has('license_key') && \Illuminate\Support\Facades\Request::has('product_id')) {
                $licenseKey = \Illuminate\Support\Facades\Request::input('license_key');
                $productId = \Illuminate\Support\Facades\Request::input('product_id');

                $url = (Utils::isNinjaDev() ? SITE_URL : NINJA_APP_URL) . "/claim_license?license_key={$licenseKey}&product_id={$productId}&get_date=true";
                $data = trim(CurlUtils::get($url));

                if ($data == RESULT_FAILURE) {
                    \Illuminate\Support\Facades\Session::flash('error', trans('texts.invalid_white_label_license'));
                } elseif ($data) {
                    $date = date_create($data)->modify('+1 year');
                    if ($date < date_create()) {
                        \Illuminate\Support\Facades\Session::flash('message', trans('texts.expired_white_label'));
                    } else {
                        $company->plan_term = PLAN_TERM_YEARLY;
                        $company->plan_paid = $data;
                        $company->plan_expires = $date->format('Y-m-d');
                        $company->plan = PLAN_WHITE_LABEL;
                        $company->save();

                        \Illuminate\Support\Facades\Session::flash('message', trans('texts.bought_white_label'));
                    }
                } else {
                    \Illuminate\Support\Facades\Session::flash('error', trans('texts.white_label_license_error'));
                }
            }
        }

        // Check data has been cached
        $cachedTables = unserialize(CACHED_TABLES);
        if (\Illuminate\Support\Facades\Request::has('clear_cache')) {
            \Illuminate\Support\Facades\Session::flash('message', 'Cache cleared');
        }
        foreach ($cachedTables as $name => $class) {
            if (\Illuminate\Support\Facades\Request::has('clear_cache') || ! \Illuminate\Support\Facades\Cache::has($name)) {
                // check that the table exists in case the migration is pending
                if ( ! \Illuminate\Support\Facades\Schema::hasTable((new $class())->getTable())) {
                    continue;
                }
                if ($name == 'paymentTerms') {
                    $orderBy = 'num_days';
                } elseif ($name == 'fonts') {
                    $orderBy = 'sort_order';
                } elseif (in_array($name, ['currencies', 'industries', 'languages', 'countries', 'banks'])) {
                    $orderBy = 'name';
                } else {
                    $orderBy = 'id';
                }
                $tableData = $class::orderBy($orderBy)->get();
                if ($tableData->count()) {
                    \Illuminate\Support\Facades\Cache::forever($name, $tableData);
                }
            }
        }

        // Show message to IE 8 and before users
        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(?i)msie [2-8]/', $_SERVER['HTTP_USER_AGENT'])) {
            \Illuminate\Support\Facades\Session::flash('error', trans('texts.old_browser', ['link' => link_to(OUTDATE_BROWSER_URL, trans('texts.newer_browser'), ['target' => '_blank'])]));
        }

        $response = $next($request);
        //$response->headers->set('X-Frame-Options', 'DENY');

        return $response;
    }
}
