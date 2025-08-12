<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckGuardPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard, string $permission): Response
    {
        $user = Auth::guard($guard)->user();
        
        if (!$user) {
            return redirect()->route($guard . '.login');
        }
        
        if (!$user->can($permission, $guard)) {
            abort(403, 'Unauthorized. Required permission: ' . $permission);
        }
        
        return $next($request);
    }
}
