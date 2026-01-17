<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    // Перенаправление на Google
    public function redirect()
    {
        session(['url.intended' => url()->previous()]);
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }
    
    // Обработка ответа от Google
    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();
    
        $user = User::updateOrCreate(
            ['google_id' => $googleUser->id],
            [
                'email' => $googleUser->email,
                'name' => $googleUser->name,
                'avatar' => $googleUser->avatar,
            ]
        );
    
        if (empty($user->permissions)) {
            $user->permissions = ['role' => 'user', 'access_level' => 'basic'];
            $user->save();
        }
    
        Auth::login($user, true);
    
        return redirect()->intended('/');
    }

}
