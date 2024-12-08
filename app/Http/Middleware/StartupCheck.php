<?php

namespace App\Http\Middleware;

use App\Events\UserLoggedIn;
use App\Libraries\CurlUtils;
use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Utils;

class StartupCheck
{
    public function handle(Request $request, Closure $next)
    {
        $this->configureTrustedProxies($request);

        if ($this->shouldRedirectToHttps($request)) {
            return Redirect::secure($request->path());
        }

        if ( ! $this->isDatabaseReady()) {
            return $next($request);
        }

        $this->validateUserAgent($request);
        $this->checkConfigAndVersion();
        $this->handleMultiDbConfiguration();
        $this->handleUserSession($request);
        $this->setApplicationLocale($request);
        $this->dispatchLoginEvents();
        //        $this->processLicenseClaims($request);
        $this->cacheData();
        $this->notifyOldBrowserUsers();

        return $next($request);
    }

    private function configureTrustedProxies(Request $request): void
    {
        if ( ! env('TRUSTED_PROXIES')) {
            return;
        }

        $proxies = env('TRUSTED_PROXIES') === '*'
            ? ['127.0.0.1', $request->server->get('REMOTE_ADDR')]
            : array_map('trim', explode(',', env('TRUSTED_PROXIES')));

        $request->setTrustedProxies($proxies, Request::HEADER_X_FORWARDED_ALL);
    }

    private function shouldRedirectToHttps(Request $request): bool
    {
        return Utils::requireHTTPS() && ! $request->secure();
    }

    private function isDatabaseReady(): bool
    {
        return Utils::isNinja() || Utils::isDatabaseSetup();
    }

    private function validateUserAgent(Request $request): void
    {
        if (Utils::isNinja() && str_contains($request->header('User-Agent'), 'Headless')) {
            abort(403);
        }
    }

    private function checkConfigAndVersion(): void
    {
        if (Utils::isSelfHost() && ! $this->isAppConfigured()) {
            echo $this->generateConfigErrorMessage();
            exit;
        }

        if (Utils::isSelfHost() && ! $this->isVersionUpdated()) {
            $this->updateVersionFile();
            Redirect::to('/update')->send();
        }
    }

    private function isAppConfigured(): bool
    {
        return env('APP_URL');
    }

    private function generateConfigErrorMessage(): string
    {
        return <<<HTML
<p>There appears to be a problem with your configuration, please check your .env file.</p>
<p>If you've run 'php artisan config:cache' you will need to run 'php artisan config:clear'.</p>
HTML;
    }

    private function isVersionUpdated(): bool
    {
        $currentVersion = @file_get_contents(storage_path('version.txt'));

        return $currentVersion === NINJA_VERSION;
    }

    private function updateVersionFile(): void
    {
        file_put_contents(storage_path('version.txt'), NINJA_VERSION);
    }

    private function handleMultiDbConfiguration(): void
    {
        if (env('MULTI_DB_ENABLED') && session()->has(SESSION_DB_SERVER)) {
            config(['database.default' => session(SESSION_DB_SERVER)]);
        }
    }

    private function handleUserSession(Request $request): void
    {
        if ( ! Auth::check()) {
            return;
        }

        $company = Auth::user()->account->company;
        Session::increment(SESSION_COUNTER, 1);

        $this->applyDiscounts($request, $company);
        $this->checkNewsFeedUpdates($request);
    }

    private function applyDiscounts(Request $request, $company): void
    {
        if ( ! Utils::isNinja() || ! $company->hasActivePlan()) {
            return;
        }

        $coupon = $request->coupon;

        $discounts = [
            config('ninja.coupon_50_off')    => 0.5,
            config('ninja.coupon_75_off')    => 0.75,
            config('ninja.coupon_free_year') => null,
        ];

        foreach ($discounts as $code => $discount) {
            if (hash_equals($coupon, $code)) {
                $this->applyCompanyDiscount($company, $discount);
                Session::flash('message', $this->generateDiscountMessage($discount));
            }
        }
    }

    private function applyCompanyDiscount($company, ?float $discount): void
    {
        if ($discount === null) {
            $company->applyFreeYear();
        } else {
            $company->applyDiscount($discount);
        }
        $company->save();
    }

    private function generateDiscountMessage(?float $discount): string
    {
        return $discount === null
            ? trans('texts.applied_free_year')
            : trans('texts.applied_discount', ['discount' => $discount * 100]);
    }

    private function checkNewsFeedUpdates(Request $request): void
    {
        $uri = $request->server('REQUEST_URI');
        if ( ! isset($uri) || str_starts_with($uri, '/news_feed') || Session::has('news_feed_id')) {
            return;
        }

        $data = Utils::isNinja()
            ? Utils::getNewsFeedResponse()
            : $this->fetchNewsFeedData();

        if ( ! $data) {
            Session::put('news_feed_id', true);

            return;
        }

        $this->handleNewsFeedData($data);
    }

    private function fetchNewsFeedData()
    {
        $url = NINJA_APP_URL . '/news_feed/' . Utils::getUserType() . '/' . NINJA_VERSION;

        return json_decode(CurlUtils::get($url));
    }

    private function handleNewsFeedData($data): void
    {
        if (version_compare(NINJA_VERSION, $data->version, '<')) {
            Session::put('news_feed_id', NEW_VERSION_AVAILABLE);
            Session::flash('news_feed_message', $this->generateUpdateMessage($data));
        } else {
            Session::put('news_feed_id', $data->id);
            if ($data->message && $data->id > Auth::user()->news_feed_id) {
                Session::flash('news_feed_message', $data->message);
            }
        }
    }

    private function generateUpdateMessage($data): string
    {
        return trans('texts.new_version_available', [
            'user_version'   => NINJA_VERSION,
            'latest_version' => $data->version,
            'releases_link'  => link_to(RELEASES_URL, 'Invoice Ninja', ['target' => '_blank']),
        ]);
    }

    private function setApplicationLocale(Request $request): void
    {
        if ($request->has('lang')) {
            $this->setLocaleFromRequest($request);
        } elseif (Auth::check()) {
            $this->setLocaleFromAccount();
        } elseif (session()->has(SESSION_LOCALE)) {
            App::setLocale(session(SESSION_LOCALE));
        }
    }

    private function setLocaleFromRequest(Request $request): void
    {
        $locale = $request->input('lang');
        App::setLocale($locale);
        session([SESSION_LOCALE => $locale]);

        if (Auth::check() && ($language = Language::whereLocale($locale)->first())) {
            $account = Auth::user()->account;
            $account->language_id = $language->id;
            $account->save();
        }
    }

    private function setLocaleFromAccount(): void
    {
        $locale = Auth::user()->account->language->locale ?? DEFAULT_LOCALE;
        App::setLocale($locale);
    }

    private function dispatchLoginEvents(): void
    {
        if (Auth::check() && ! Session::has(SESSION_TIMEZONE)) {
            Event::dispatch(new UserLoggedIn());
        }
    }

    private function processLicenseClaims(Request $request): void
    {
        if (Utils::isNinjaProd() || ! $request->has(['license_key', 'product_id'])) {
            return;
        }

        $licenseKey = $request->input('license_key');
        $productId = $request->input('product_id');
        $url = $this->generateLicenseClaimUrl($licenseKey, $productId);

        $response = trim(CurlUtils::get($url));
        $this->handleLicenseResponse($response);
    }

    private function generateLicenseClaimUrl(string $licenseKey, string $productId): string
    {
        $base = Utils::isNinjaDev() ? SITE_URL : NINJA_APP_URL;

        return sprintf('%s/claim_license?license_key=%s&product_id=%s&get_date=true', $base, $licenseKey, $productId);
    }

    private function handleLicenseResponse(string $response): void
    {
        if ($response === RESULT_FAILURE) {
            Session::flash('error', trans('texts.invalid_white_label_license'));
        } elseif ( ! empty($response) && $response !== '0') {
            $this->processValidLicenseResponse($response);
        } else {
            Session::flash('error', trans('texts.white_label_license_error'));
        }
    }

    private function processValidLicenseResponse(string $response): void
    {
        $date = date_create($response)->modify('+1 year');
        if ($date < date_create()) {
            Session::flash('message', trans('texts.expired_white_label'));
        } else {
            $this->updateLicensePlan($date);
            Session::flash('message', trans('texts.bought_white_label'));
        }
    }

    private function updateLicensePlan($date): void
    {
        $company = Auth::user()->account->company;
        $company->plan_term = PLAN_TERM_YEARLY;
        $company->plan_paid = $date->format('Y-m-d');
        $company->plan_expires = $date->format('Y-m-d');
        $company->plan = PLAN_WHITE_LABEL;
        $company->save();
    }

    private function cacheData(): void
    {
        if (request()->has('clear_cache')) {
            Session::flash('message', 'Cache cleared');
        }

        foreach ($this->getCachedTables() as $name => $class) {
            if ($this->shouldCacheTable($name)) {
                $this->cacheTableData($name, $class);
            }
        }
    }

    private function getCachedTables(): array
    {
        return unserialize(CACHED_TABLES);
    }

    private function shouldCacheTable(string $name): bool
    {
        return request()->has('clear_cache') || ! Cache::has($name);
    }

    private function cacheTableData(string $name, string $class): void
    {
        if ( ! Schema::hasTable((new $class())->getTable())) {
            return;
        }

        $orderBy = $this->getTableOrderBy($name);
        $data = $class::orderBy($orderBy)->get();

        if ($data->isNotEmpty()) {
            Cache::forever($name, $data);
        }
    }

    private function getTableOrderBy(string $name): string
    {
        return match ($name) {
            'paymentTerms' => 'num_days',
            'fonts'        => 'sort_order',
            'currencies', 'industries', 'languages', 'countries', 'banks' => 'name',
            default => 'id',
        };
    }

    private function notifyOldBrowserUsers(): void
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(?i)msie [2-8]/', $_SERVER['HTTP_USER_AGENT'])) {
            Session::flash('error', trans('texts.old_browser', [
                'link' => link_to(OUTDATE_BROWSER_URL, trans('texts.newer_browser'), ['target' => '_blank']),
            ]));
        }
    }
}
