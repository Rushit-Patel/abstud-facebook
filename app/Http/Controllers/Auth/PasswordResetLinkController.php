<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
        ]);

        // Find user by username and get their email for password reset
        $user = \App\Models\User::where('username', $request->username)->first();
        
        if ($user) {
            Password::sendResetLink(['email' => $user->email]);
        }

        return back()->with('status', __('A reset link will be sent if the account exists.'));
    }
}
