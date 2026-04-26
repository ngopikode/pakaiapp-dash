<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::livewire('login', 'pages::auth.login')->name('login');
});
