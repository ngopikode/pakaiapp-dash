<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::livewire('login', 'pages::auth.login')->name('login');
    Route::livewire('forgot-password', 'pages::auth.forgot-password')->name('password.request');
    Route::livewire('reset-password', 'pages::auth.reset-password')->name('password.reset');
});
