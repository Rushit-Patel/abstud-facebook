<?php

namespace App\Http\Controllers\Team;

use Illuminate\Http\Request;
use App\Models\FcmToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        FcmToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'type'    => $user->hasRole('partner') ? 'partner' : 'user',
            ],
            [
                'token' => $request->token,
            ]
        );

        return response()->json(['message' => 'Token saved successfully.']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        FcmToken::where('token', $request->token)->delete();

        return response()->json(['message' => 'Token deleted.']);
    }
}
