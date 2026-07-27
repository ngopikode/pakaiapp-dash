<?php

use App\Central\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::livewire('login', 'pages::auth.login')->name('login');
    Route::livewire('forgot-password', 'pages::auth.forgot-password')->name('password.request');
    Route::livewire('reset-password', 'pages::auth.reset-password')->name('password.reset');

    Route::get('auto-login', function (Request $request) {
        $token = $request->query('token');
        if (!$token) {
            return redirect()->route('login');
        }

        $email = Cache::get('auto_login_' . $token);
        if (!$email) {
            return redirect()->route('login');
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user);
            Cache::forget('auto_login_' . $token);
            // Minta mereka ganti password setelah registrasi
            session()->flash('success', 'Pendaftaran berhasil! Anda telah login otomatis. Silakan ubah password Anda sekarang demi keamanan.');

            return redirect()->route('profile');
        }

        return redirect()->route('login');
    });
});
