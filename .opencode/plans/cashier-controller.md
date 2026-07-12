# Plan: CashierController — ganti Route::livewire dengan controller

## Konteks
User ikutin pola `HomeController::class` (route `tenant.php` line 37) untuk cashier. Pindahin logika `storeType` + percabangan blade ke controller, view bersih.

## Target file
1. **NEW** `app/Tenant/Controllers/Web/CashierController.php`
2. **EDIT** `routes/tenant.php`
3. **EDIT** `resources/views/layouts/app.blade.php`
4. **EDIT** `resources/views/pages/tenant/pos/⚡index/index.blade.php`
5. **DELETE** `resources/views/pages/tenant/pos/⚡index/index.php` (Volt class, no longer needed)

## Detail

### 1. CashierController.php
```php
<?php
declare(strict_types=1);

namespace App\Tenant\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class CashierController extends Controller
{
    public function __invoke(): View
    {
        $storeType = tenant('store_type') ?? 'resto';

        return view('pages.tenant.pos.index', [
            'componentName' => $storeType === 'resto'
                ? 'pages::tenant.pos.resto-cashier'
                : 'pages::tenant.pos.retail-cashier',
            'addToOrder'   => request()->query('add_to_order'),
            'title'        => 'Kasir',
            'navbar'       => ['mode' => 'pos', 'title' => 'PakaiApp POS'],
        ]);
    }
}
```
— Pattern sama kayak `HomeController`, tambah pass `$navbar` + `$title` ke view.

### 2. routes/tenant.php
```diff
- Route::livewire('cashier', 'pages::tenant.pos.index')->name('cashier');
+ use App\Tenant\Controllers\Web\CashierController;
+ Route::get('cashier', CashierController::class)->name('cashier');
```

### 3. app.blade.php (layout)
Hapus route detection:
```diff
- $routeName = request()->route()?->getName();
- $isPos = $routeName === 'cashier';
```

Ganti semua cek `$isPos` → cek `$navbar` array:
```diff
- :header="$isPos ? ['mode' => 'pos', 'title' => 'PakaiApp POS'] : ($title ?? null)"
+ :header="$navbar ?? ($title ?? null)"
```
```diff
- @if($isPos) !p-0 overflow-hidden @endif
+ @if(is_array($navbar ?? null) && ($navbar['mode'] ?? null) === 'pos') !p-0 overflow-hidden @endif
```

### 4. index.blade.php
Dari percabangan:
```blade
<div>
    @if($storeType === 'resto')
        <livewire:pages::tenant.pos.resto-cashier :addToOrder="$addToOrder" />
    @elseif($storeType === 'retail')
        <livewire:pages::tenant.pos.retail-cashier/>
    @else
        <div class="alert alert-danger...">Tipe toko tidak valid</div>
    @endif
</div>
```
Jadi 1 baris:
```blade
<div>
    @if(isset($componentName))
        @livewire($componentName, ['addToOrder' => $addToOrder])
    @endif
</div>
```

Plus `@extends('layouts.app')` DI ATAS? **TIDAK.** Controller return `view('pages.tenant.pos.index', $data)` — ini Blade biasa tanpa layout. Tapi `layouts.app` compute `$showSidebar` dll sendiri. Kita perlu layout wrapping.

Cek: `HomeController` return view `pages.tenant.store.resto.index` — view itu pake `<x-layouts::store>`. Store views punya layout sendiri.

Untuk cashier view dibungkus `layouts.app` → controller pake `->layout()`:
```php
return view('pages.tenant.pos.index', $data);
```
Ini render Blade biasa TANPA layout. `$slot` di `layouts.app` ga akan nangkep.

### Opsi fix layout wrapping:
**A)** Tambah `@extends('layouts.app')` di `index.blade.php` — `$title` + `$navbar` dari controller otomatis kebaca layout. Paling simpel. Tapi kalo file ini masih dipake Livewire FC (sebelum route diganti), double-wrap.

**B)** Controller return `view(...)` tapi Laravel bungkus otomatis via middleware atau service provider. Butuh infrastruktur tambahan.

**C)** KEEP `Route::livewire` — jangan controller, tapi pindahin logic ke `index.php`. Bedanya: tes `StoreSetting` di PHP class, bukan blade. Blade tetap ada conditional `@if(storeType)` tapi cuma 1 tag `@livewire`. Bukan controller-approach tapi decent.

Gue rekomendasi **A** — paling simpel, pola Laravel standar. Double-wrap ga terjadi karena route udah diganti dari `Route::livewire` ke `Route::get`. Livewire ga akan auto-wrap view dari controller.

### 5. index.php
Hapus (`pages::tenant.pos.index` ga dipake route/livewire lagi kecuali ada referensi lain). Atau keep kosong dengan `new class extends Component {}` biar Volt ga error.

Cek referensi:
```
# Pastikan ga ada yang manggil pages::tenant.pos.index selain route cashier
```

## Ringkasan eksekusi
1. Buat `CashierController.php`
2. `routes/tenant.php`: `livewire` → `get` + import controller
3. `index.blade.php`: `@extends('layouts.app')` + 1 baris `@livewire`
4. `app.blade.php`: hapus `$routeName`/`$isPos`, ganti ke cek `$navbar`
5. `index.php`: hapus (atau kosongin)
6. `php -l` semua, `npm run build`, test route `/cashier`
