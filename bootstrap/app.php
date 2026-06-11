<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\DetectDevice;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/auth/login',
        );
        $middleware->group('universal', []);
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
        $middleware->web(append: [
            DetectDevice::class,
        ]);
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
            throw new NotFoundHttpException('Halaman atau Toko yang Anda tuju tidak dapat ditemukan.');
        });
    })->create();
