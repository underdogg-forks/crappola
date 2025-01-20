<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EligibleForMigration
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->eligibleForMigration()) {
            return $next($request);
        }

        return redirect('/settings/account_management');
    }
}
