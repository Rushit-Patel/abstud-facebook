<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckGuardRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard, string $role): Response
    {
        $user = Auth::guard($guard)->user();
        
        if (!$user) {
            return redirect()->route($guard . '.login');
        }
        
        if (!$user->hasRole($role, $guard)) {
            abort(403, 'Unauthorized. Required role: ' . $role);
        }
        
        return $next($request);
    }
}
