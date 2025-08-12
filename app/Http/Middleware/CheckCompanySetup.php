<?php

namespace App\Http\Middleware;

use App\Models\CompanySetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanySetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip setup check for setup routes
        if ($request->routeIs('setup.*')) {
            return $next($request);
        }

        // Check if company setup is completed
        if (!CompanySetting::isSetupCompleted()) {
            return redirect()->route('setup.company');
        }

        return $next($request);
    }
}
