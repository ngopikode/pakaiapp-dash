<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::livewire('login', 'pages::auth.login')->name('login');
    Route::livewire('forgot-password', 'pages::auth.forgot-password')->name('password.request');
    Route::livewire('reset-password', 'pages::auth.reset-password')->name('password.reset');

    Route::get('auto-login', function (\Illuminate\Http\Request $request) {
        $token = $request->query('token');
        if (!$token) {
            return redirect()->route('login');
        }

        $email = \Illuminate\Support\Facades\Cache::get('auto_login_' . $token);
        if (!$email) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::where('email', $email)->first();
        if ($user) {
            \Illuminate\Support\Facades\Auth::login($user);
            \Illuminate\Support\Facades\Cache::forget('auto_login_' . $token);
            // Minta mereka ganti password setelah registrasi
            session()->flash('success', 'Pendaftaran berhasil! Anda telah login otomatis. Silakan ubah password Anda sekarang demi keamanan.');
            return redirect()->route('profile');
        }

        return redirect()->route('login');
    });
});
