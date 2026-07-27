<?php

use App\Shared\Middleware\CheckRole;
use App\Shared\Middleware\CheckStoreOpen;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up'
    )
    ->withEvents(discover: [
        __DIR__ . '/../app/Shared/Listeners',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/auth/login',
        );
        $middleware->group('universal', []);
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role'       => CheckRole::class,
            'store.open' => CheckStoreOpen::class,
        ]);
        $middleware->web();
        $middleware->preventRequestForgery(except: [
            'duitku/callback',
            'midtrans/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            TenantCouldNotBeIdentifiedOnDomainException::class,
        ]);

        $exceptions->render(function (TenantCouldNotBeIdentifiedOnDomainException $e, \Illuminate\Http\Request $request) {
            throw new NotFoundHttpException(message: 'Halaman atau Toko yang Anda tuju tidak dapat ditemukan.');
        });
    })->create();
