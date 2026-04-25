<?php

use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::livewire('/', 'pages::central.register-tenant')->name('home');
    });
}
