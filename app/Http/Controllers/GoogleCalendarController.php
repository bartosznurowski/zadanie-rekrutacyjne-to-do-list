<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleCalendarController extends Controller
{
    public function redirect() {
    return Socialite::driver('google')
        ->scopes(['openid', 'profile', 'email', 'https://www.googleapis.com/auth/calendar'])
        ->with(['access_type' => 'offline', 'prompt' => 'consent'])
        ->redirect();
}


    public function callback() {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = auth()->user();
        
        $user->update([
            'google_token' => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token,
            'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn),
        ]);

        return redirect()->route('tasks.index')->with('success', 'Google Calendar połączony!');
    }
}