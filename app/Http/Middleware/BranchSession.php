<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next): Response
    {
        
        if (!session()->has('branch')) {
            return redirect()->route('client.guest.no-session-branch', ['branchId' => ''])->with('error', 'Please select a branch first.');
        }
        return $next($request);
    }
}
