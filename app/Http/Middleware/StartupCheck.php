<?php

namespace App\Http\Middleware;

use App;
use App\Events\UserLoggedIn;
use App\Libraries\CurlUtils;
use App\Models\Language;
use Auth;
use Cache;
use Closure;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\Request;
use Redirect;
use Schema;
use Session;
use App\Libraries\Utils;

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
                $request->setTrustedProxies(['127.0.0.1', $request->server->get('REMOTE_ADDR')]);
            } else {
                $request->setTrustedProxies(array_map('trim', explode(',', env('TRUSTED_PROXIES'))));
            }
        }

        // Ensure all request are over HTTPS in production
        if (Utils::requireHTTPS() && !$request->secure()) {
            return Redirect::secure($request->path());
        }

        // If the database doens't yet exist we'll skip the rest
        if (!Utils::isNinja() && !Utils::isDatabaseSetup()) {
            return $next($request);
        }

        // Check to prevent headless browsers from triggering activity
        if (Utils::isNinja() && !$request->phantomjs && strpos($request->header('User-Agent'), 'Headless') !== false) {
            abort(403);
        }

        if (Utils::isSelfHost()) {
            // Check if config:cache may have been run
            if (app()->configurationIsCached()) {
                echo 'Config caching is not currently supported, please run the following command to clear the cache.<pre>php artisan config:clear</pre>';
                exit;
            }

            // Check if a new version was installed
            $file = storage_path() . '/version.txt';
            $version = @file_get_contents($file);
            if ($version != NINJA_VERSION) {
                if (version_compare(phpversion(), '7.0.0', '<')) {
                    dd('Please update PHP to >= 7.0.0');
                }
                $handle = fopen($file, 'w');
                fwrite($handle, NINJA_VERSION);
                fclose($handle);

                return Redirect::to('/update');
            }
        }

        if (env('MULTI_DB_ENABLED')) {
            if ($server = session(SESSION_DB_SERVER)) {
                config(['database.default' => $server]);
            }
        }

        if (Auth::check()) {
            $companyPlan = Auth::user()->company->companyPlan;
            $count = Session::get(SESSION_COUNTER, 0);
            Session::put(SESSION_COUNTER, ++$count);

            if (Utils::isNinja()) {
                if (($coupon = request()->coupon) && !$companyPlan->hasActivePlan()) {
                    if ($code = config('ninja.coupon_50_off')) {
                        if (hash_equals($coupon, $code)) {
                            $companyPlan->applyDiscount(.5);
                            $companyPlan->save();
                            Session::flash('message', trans('texts.applied_discount', ['discount' => 50]));
                        }
                    }
                    if ($code = config('ninja.coupon_75_off')) {
                        if (hash_equals($coupon, $code)) {
                            $companyPlan->applyDiscount(.75);
                            $companyPlan->save();
                            Session::flash('message', trans('texts.applied_discount', ['discount' => 75]));
                        }
                    }
                    if ($code = config('ninja.coupon_free_year')) {
                        if (hash_equals($coupon, $code)) {
                            $companyPlan->applyFreeYear();
                            $companyPlan->save();
                            Session::flash('message', trans('texts.applied_free_year'));
                        }
                    }
                }
            }

            // Check the application is up to date and for any news feed messages
            if (isset($_SERVER['REQUEST_URI']) && !Utils::startsWith($_SERVER['REQUEST_URI'], '/news_feed') && !Session::has('news_feed_id')) {
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
                            'user_version' => NINJA_VERSION,
                            'latest_version' => $data->version,
                            'releases_link' => link_to(RELEASES_URL, 'Invoice Ninja', ['target' => '_blank']),
                        ];
                        Session::put('news_feed_id', NEW_VERSION_AVAILABLE);
                        Session::flash('news_feed_message', trans('texts.new_version_available', $params));
                    } else {
                        Session::put('news_feed_id', $data->id);
                        if ($data->message && $data->id > Auth::user()->news_feed_id) {
                            Session::flash('news_feed_message', $data->message);
                        }
                    }
                } else {
                    Session::put('news_feed_id', true);
                }
            }
        }

        // Check if we're requesting to change the company's language
        if (request()->has('lang')) {
            $locale = $request->get('lang');
            App::setLocale($locale);
            session([SESSION_LOCALE => $locale]);

            if (Auth::check()) {
                if ($language = Language::whereLocale($locale)->first()) {
                    $company = Auth::user()->company;
                    $company->language_id = $language->id;
                    $company->save();
                }
            }
        } elseif (Auth::check()) {
            $locale = Auth::user()->company->language ? Auth::user()->company->language->locale : DEFAULT_LOCALE;
            App::setLocale($locale);
        } elseif (session(SESSION_LOCALE)) {
            App::setLocale(session(SESSION_LOCALE));
        }

        // Make sure the company/user localization settings are in the session
        if (Auth::check() && !Session::has(SESSION_TIMEZONE)) {
            Event::dispatch(new UserLoggedIn());
        }

        // Check if the user is claiming a license (ie, additional invoices, white label, etc.)
        if (!Utils::isNinjaProd() && isset($_SERVER['REQUEST_URI'])) {
            $claimingLicense = Utils::startsWith($_SERVER['REQUEST_URI'], '/claim_license');
            if (!$claimingLicense && request()->has('license_key') && request()->has('product_id')) {
                $licenseKey = $request->get('license_key');
                $productId = $request->get('product_id');

                $url = (Utils::isNinjaDev() ? SITE_URL : NINJA_APP_URL) . "/claim_license?license_key={$licenseKey}&product_id={$productId}&get_date=true";
                $data = trim(CurlUtils::get($url));

                if ($data == RESULT_FAILURE) {
                    Session::flash('error', trans('texts.invalid_white_label_license'));
                } elseif ($data) {
                    $date = date_create($data)->modify('+1 year');
                    if ($date < date_create()) {
                        Session::flash('message', trans('texts.expired_white_label'));
                    } else {
                        $companyPlan->plan_term = PLAN_TERM_YEARLY;
                        $companyPlan->plan_paid = $data;
                        $companyPlan->plan_expires = $date->format('Y-m-d');
                        $companyPlan->plan = PLAN_WHITE_LABEL;
                        $companyPlan->save();

                        Session::flash('message', trans('texts.bought_white_label'));
                    }
                } else {
                    Session::flash('error', trans('texts.white_label_license_error'));
                }
            }
        }

        // Check data has been cached
        $cachedTables = unserialize(CACHED_TABLES);
        if (request()->has('clear_cache')) {
            Session::flash('message', 'Cache cleared');
        }
        foreach ($cachedTables as $name => $class) {
            if (request()->has('clear_cache') || !Cache::has($name)) {
                // check that the table exists in case the migration is pending
                if (!Schema::hasTable((new $class())->getTable())) {
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
                    Cache::forever($name, $tableData);
                }
            }
        }

        // Show message to IE 8 and before users
        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(?i)msie [2-8]/', $_SERVER['HTTP_USER_AGENT'])) {
            Session::flash('error', trans('texts.old_browser', ['link' => link_to(OUTDATE_BROWSER_URL, trans('texts.newer_browser'), ['target' => '_blank'])]));
        }

        $response = $next($request);
        //$response->headers->set('X-Frame-Options', 'DENY');

        return $response;
    }
}
