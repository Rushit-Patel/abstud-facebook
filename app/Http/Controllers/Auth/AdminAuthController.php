<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.team.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Check if user exists and is active
        $user = User::where('username', $request->username)->where('is_active', true)->first();
        
        if (!$user) {
            throw ValidationException::withMessages([
                'username' => 'Invalid credentials or inactive account.',
            ]);
        }

        // Attempt authentication
        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            
            $request->session()->regenerate();

            return redirect()->intended(route('team.dashboard'));
        }

        throw ValidationException::withMessages([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('team.login');
    }
}