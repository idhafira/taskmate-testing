<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    // FR-001
    public function register(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create($data); // password di-hash otomatis oleh cast 'hashed' (Bcrypt)

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat. Silakan masuk.');
    }

    // FR-002, FR-003 (kunci akun 15 menit setelah 5 kali gagal)
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user && $user->terkunci_sampai && $user->terkunci_sampai->isFuture()) {
            return back()->withErrors([
                'email' => 'Akun terkunci sampai '.$user->terkunci_sampai->format('d/m/Y H:i').' WITA.',
            ])->onlyInput('email');
        }

        if ($user && Hash::check($data['password'], $user->password)) {
            $user->update(['gagal_login' => 0, 'terkunci_sampai' => null]);
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('workspace.index');
        }

        $pesan = 'Email atau kata sandi salah.';

        if ($user) {
            $gagal = $user->gagal_login + 1;

            if ($gagal >= 5) {
                $user->update(['gagal_login' => 0, 'terkunci_sampai' => now()->addMinutes(15)]);
                $pesan = 'Gagal login 5 kali. Akun dikunci selama 15 menit.';
            } else {
                $user->update(['gagal_login' => $gagal]);
            }
        }

        return back()->withErrors(['email' => $pesan])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
