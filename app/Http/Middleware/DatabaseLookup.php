<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DatabaseLookup
{
    public function handle(Request $request, Closure $next, $guard = 'user')
    {
        if ( ! env('MULTI_DB_ENABLED')) {
            return $next($request);
        }
        return null;
    }
}
