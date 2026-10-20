<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    
    public function callback()
    {
        try {
            $google = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Login dengan Google gagal, silakan coba lagi.']);
        }

        
        
        $user = User::where('google_id', $google->getId())
            ->orWhere('email', $google->getEmail())
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $google->getId(),
                'avatar'    => $google->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'nama'              => $google->getName(), // Diubah dari 'name' menjadi 'nama'
                'email'             => $google->getEmail(),
                'google_id'         => $google->getId(),
                'avatar'            => $google->getAvatar(),
                'password'          => Str::random(32),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->route('workspace.index'); // sesuaikan dengan halaman utama Anda
    }
}