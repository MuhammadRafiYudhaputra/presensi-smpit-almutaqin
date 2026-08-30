<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        // Cek apakah alamat email terdaftar
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Alamat email yang Anda masukkan tidak terdaftar dalam sistem.',
            ])->onlyInput('email');
        }

        // Cek apakah password cocok
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Kata sandi yang Anda masukkan salah. Silakan periksa kembali.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->role === 'guru') {
                return redirect()->intended(route('guru.monitoring'))
                    ->with('success', "Selamat datang kembali, {$user->name}!");
            }

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'Gagal melakukan login. Silakan periksa kembali email dan kata sandi Anda.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email yang Anda masukkan tidak terdaftar di sistem.',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        // Reset password ke default yang aman
        $defaultPassword = ($user->role === 'admin') ? 'admin123' : '12345678';
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($defaultPassword),
        ]);

        return redirect()->route('login')->with('success', "Password untuk akun [{$user->email}] telah berhasil di-reset menjadi: {$defaultPassword}. Silakan masuk kembali.");
    }
}
