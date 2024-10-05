<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;
use Utils;

/**
 * Class QueryLogging.
 */
class QueryLogging
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
        // Enable query logging for development
        if (Utils::isNinjaDev()) {
            \Illuminate\Support\Facades\DB::enableQueryLog();
            $timeStart = microtime(true);
        }

        $response = $next($request);

        // hide requests made by debugbar
        if (Utils::isNinjaDev() && mb_strstr($request->url(), '_debugbar') === false) {
            $queries = \Illuminate\Support\Facades\DB::getQueryLog();
            $count = count($queries);
            $timeEnd = microtime(true);
            $time = $timeEnd - $timeStart;
            \Illuminate\Support\Facades\Log::info($request->method() . ' - ' . $request->url() . sprintf(': %d queries - ', $count) . $time);
            //Log::info($queries);
        }

        return $response;
    }
}
