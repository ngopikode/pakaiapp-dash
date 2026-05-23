<?php

declare(strict_types=1);

use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\OrderHistoryApiController;
use App\Http\Controllers\Api\RestaurantApiController;
use App\Http\Controllers\MenuController;
use App\Http\Middleware\FileUrlMiddleware;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    FileUrlMiddleware::class,
])->group(function () {

    Route::livewire('/invoice/{code}', 'pages::tenant.invoice.show')->name('invoice.show');

    Route::get('/', function () {
        if (tenant('store_type') === 'retail') return view('pages.tenant.retail.index');
        return view('pages.tenant.store.resto.index');
    })->name('index');

    // routes/web.php

    Route::get('/menu/{product}',
        fn(Product $product) => view('pages.tenant.store.resto.product', compact('product'))
            ->with('product', $product->load(['variants', 'extras']))
    )->name('product.show');

    Route::get('/menu/{product}/story', [MenuController::class, 'shareAsStory'])->name('product.story');

    Route::middleware('auth')->group(function () {

        // Routes accessible by manager AND cashier
        Route::middleware('role:manager,cashier')->group(function () {
            Route::livewire('cashier', 'pages::tenant.pos.index')->name('cashier');
            Route::view('order', 'pages.tenant.order.index')->name('order');
            Route::livewire('profile', 'pages::tenant.profile.user-profile')->name('profile');
        });

        // Routes accessible ONLY by manager
        Route::middleware('role:manager')->group(function () {
            Route::livewire('dashboard', 'pages::tenant.dashboard')->name('dashboard');
            Route::livewire('wallet', 'pages::tenant.payment.wallet')->name('wallet');
            Route::view('product', 'pages.tenant.product.product')->name('product');
            Route::livewire('product/create', 'pages::tenant.product.form')->name('product.create');
            Route::livewire('product/{product}/edit', 'pages::tenant.product.form')->name('product.edit');
            Route::livewire('store-setting', 'pages::tenant.setting.store-setting')->name('store-setting');
            Route::livewire('product-slot/buy', 'pages::tenant.setting.buy-product-slot')->name('product-slot.buy');
            Route::view('user', 'pages.tenant.user.index')->name('user');
        });
    });

    Route::prefix('api')->middleware(['api'])->group(function () {
        Route::get('/restaurant', RestaurantApiController::class);
        Route::post('/orders', [OrderApiController::class, 'store'])->middleware('throttle:orders');
        Route::post('/orders/history', [OrderHistoryApiController::class, 'index'])->middleware('throttle:30,1');

        // ─── Duitku — callback/return/status sudah pindah ke central domain ────
        // Payment methods: tetap di tenant karena butuh context tenant untuk amount
        Route::get('/duitku/payment-methods', function (\Illuminate\Http\Request $request) {
            if (!config('duitku.enabled')) {
                abort(403, 'Duitku payment gateway is disabled.');
            }
            $request->validate(['amount' => 'required|numeric|min:1']);
            try {
                $service = new \App\Services\DuitkuService();
                $methods = $service->getPaymentMethods((int)$request->amount);
                return response()->json(['success' => true, 'data' => $methods]);
            } catch (Throwable $e) {
                \Illuminate\Support\Facades\Log::error('[Duitku] getPaymentMethods error', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'Gagal mengambil metode pembayaran.'], 500);
            }
        })->name('duitku.payment-methods');
    });

    require __DIR__ . '/auth.php';
});
