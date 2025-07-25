<?php

namespace App\Http\Middleware;

use App\Libraries\Utils;
use Closure;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            DB::enableQueryLog();
            $timeStart = microtime(true);
        }

        $response = $next($request);

        if (Utils::isNinjaDev()) {
            // hide requests made by debugbar
            if (mb_strstr($request->url(), '_debugbar') === false) {
                $queries = DB::getQueryLog();
                $count = count($queries);
                $timeEnd = microtime(true);
                $time = $timeEnd - $timeStart;
                Log::info($request->method() . ' - ' . $request->url() . ": {$count} queries - " . $time);
                //Log::info($queries);
            }
        }

        return $response;
    }
}
