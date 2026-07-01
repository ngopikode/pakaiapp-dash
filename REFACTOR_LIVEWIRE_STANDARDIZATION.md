# Livewire 4 Standardization — Tenant Routes & Store Layout

## Background

Proyek ini menggunakan Livewire 4 dengan pola `Route::livewire()` dan `<livewire:tag />` di layout.
Ada beberapa masalah struktural saat ini:

1. **`routes/tenant.php`** — Urutan dan pengelompokan route kurang terstruktur. Section *public*, *auth*, dan *api*
   tidak dipisahkan secara visual dan tidak berurutan logis.
2. **`resources/views/layouts/store.blade.php`** — File ini sangat panjang (370 baris), mencampur logika PHP kompleks,
   markup HTML, Alpine.js, dan beberapa `@include` inline. Sulit dibaca dan di-maintain.
3. **`.docs/references/livewire4/`** — Struktur referensi upstream sudah ada, tapi belum ada satu pun file panduan
   standarisasi project-spesifik (`STANDARDS.md`) yang menjadi acuan tim.

---

## Scope & Constraints

- **Non-breaking** — Tidak ada perubahan perilaku, hanya perubahan struktur/keterbacaan.
- **Referensi upstream** di `.docs/references/livewire4/` **tidak diubah**, hanya ditambah satu file baru.
- Semua HTML output tetap identik setelah refaktor.

---

## Open Questions

> **[?] Route `/` — HomeController vs Livewire Page Component**
>
> `HomeController` mendeteksi `store_type` (resto vs retail) dan me-return view berbeda.
> Ada dua opsi:
> - **Opsi A** *(dipakai di plan ini)*: Biarkan `/` tetap `Route::get()` + `HomeController`. Logika conditional ini
    tidak cocok untuk single Livewire page component.
> - **Opsi B**: Ubah ke `Route::livewire()` dengan komponen page yang menggunakan `#[Layout]` attribute berbeda per
    `store_type`.
>
> **→ Mohon konfirmasi opsi yang dipilih sebelum eksekusi.**

---

## Proposed Changes

### 1. `.docs/references/livewire4/STANDARDS.md` `[NEW]`

File panduan standarisasi baru sebagai **single source of truth** untuk tim.

**Isi yang akan dibuat:**

| Topik                                  | Keterangan                                                       |
|----------------------------------------|------------------------------------------------------------------|
| Konvensi namespace                     | `pages::`, `components::`, aturan penamaan sub-direktori         |
| Route::livewire() vs Controller        | Kapan pakai masing-masing                                        |
| Layout & `$slot`                       | Struktur layout file yang direkomendasikan                       |
| Embedded Livewire di Layout            | Kapan `<livewire:tag />` boleh ada di layout file                |
| `@livewireStyles` / `@livewireScripts` | Penempatan yang benar                                            |
| Alpine.js + `@script`                  | Kapan `Alpine.data()` harus di `@script` vs file `.js` eksternal |
| Partial conventions                    | Prefix `_` untuk partials, lokasi per domain                     |

**Referensi upstream yang akan di-link:**

- `.docs/references/livewire4/essentials/pages.md`
- `.docs/references/livewire4/essentials/components.md`
- `.docs/references/livewire4/mfc-alpine-architecture.md`
- `.docs/references/livewire4/php-attributes/attribute-layout.md`

---

### 2. `routes/tenant.php` `[MODIFY]`

**Masalah saat ini:**

```php
// Tidak ada section separator yang jelas
Route::livewire('/invoice/{code}', ...)->name('invoice.show');   // public
Route::livewire('/order/{code}', ...)->name('order.show');       // public
Route::get('/manifest.json', TenantManifestController::class);  // public
Route::get('/', HomeController::class)->name('index');           // public
Route::controller(MenuController::class)->prefix('menu')...      // public
Route::middleware('auth')->group(...);                           // auth
Route::prefix('api')->middleware(['api'])->group(...);           // api
```

**Setelah refaktor:**

```php
/*
|──────────────────────────────────────────────────────────────────────────────
| Public Routes
|──────────────────────────────────────────────────────────────────────────────
*/
Route::get('/', HomeController::class)->name('index');
Route::get('/manifest.json', TenantManifestController::class);
Route::controller(MenuController::class)->prefix('menu')->name('product.')->group(function () {
    Route::get('/{product}', 'show')->name('show');
    Route::get('/{product}/story', 'shareAsStory')->name('story');
});
Route::livewire('/invoice/{code}', 'pages::tenant.invoice.show')->name('invoice.show');
Route::livewire('/order/{code}', 'pages::tenant.order.show')->name('order.show');

/*
|──────────────────────────────────────────────────────────────────────────────
| Authenticated Routes
|──────────────────────────────────────────────────────────────────────────────
*/
Route::middleware('auth')->group(function () {
    // manager & cashier
    Route::middleware('role:manager,cashier')->group(function () { ... });
    // manager & kitchen
    Route::middleware('role:manager,kitchen')->group(function () { ... });
    // manager only
    Route::middleware('role:manager')->group(function () { ... });
});

/*
|──────────────────────────────────────────────────────────────────────────────
| API Routes
|──────────────────────────────────────────────────────────────────────────────
*/
Route::prefix('api')->middleware(['api'])->group(function () { ... });

require __DIR__ . '/auth.php';
```

**Perubahan `use` statements** — Diurutkan alfabetis per group:

```diff
- use App\Tenant\Controllers\Api\DuitkuApiController;
- use App\Tenant\Controllers\Api\OrderApiController;
- use App\Tenant\Controllers\Api\OrderHistoryApiController;
- use App\Tenant\Controllers\Api\RestaurantApiController;
- use App\Tenant\Controllers\Web\HomeController;
- use App\Tenant\Controllers\Web\MenuController;
- use App\Tenant\Controllers\Web\TenantManifestController;
- use App\Shared\Middleware\FileUrlMiddleware;
- use Illuminate\Support\Facades\Route;
- use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
- use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
+ // Controllers — API
+ use App\Tenant\Controllers\Api\DuitkuApiController;
+ use App\Tenant\Controllers\Api\OrderApiController;
+ use App\Tenant\Controllers\Api\OrderHistoryApiController;
+ use App\Tenant\Controllers\Api\RestaurantApiController;
+ // Controllers — Web
+ use App\Tenant\Controllers\Web\HomeController;
+ use App\Tenant\Controllers\Web\MenuController;
+ use App\Tenant\Controllers\Web\TenantManifestController;
+ // Middleware & Support
+ use App\Shared\Middleware\FileUrlMiddleware;
+ use Illuminate\Support\Facades\Route;
+ use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
+ use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
```

---

### 3. `resources/views/layouts/store.blade.php` `[MODIFY]` + 3 Partial Baru `[NEW]`

**Masalah saat ini (370 baris):**

```
store.blade.php (370 baris)
├── @php — logika kompleks (WA, orderTypes) ← OK, tetap di sini
├── @if (!$setting) — HTML fallback inline (30 baris) ← Pisahkan
├── <head> — semua meta, OG, vite, scripts inline (50 baris) ← Pisahkan
├── @include(_loader) ← OK
├── <div x-data="storeApp"> — root Alpine element
│   ├── Pull-to-refresh ← Tetap (terikat ke x-data)
│   ├── @include(_hero) ← OK
│   ├── {{ $slot }} ← OK
│   ├── Contact Modal (inline, ~90 baris) ← Pisahkan ke store-globals
│   ├── Toast (inline, ~15 baris) ← Pisahkan ke store-globals
│   ├── QR Modal (inline, ~60 baris) ← Pisahkan ke store-globals
│   ├── @include(option-modal) ← OK
│   ├── @include(checkout-modal) ← OK
│   ├── @include(history-modal) ← OK
│   └── <livewire:components::tenant.ai-floating-chat/> ← OK
└── @livewireScripts ← OK
```

**Struktur partial baru:**

```
resources/views/layouts/
├── store.blade.php                    ← MODIFY: ~90 baris (dari 370)
└── _partials/
    ├── store-not-found.blade.php      ← NEW: HTML "toko tidak aktif" (~30 baris)
    ├── store-head.blade.php           ← NEW: semua <head> content (~50 baris)
    └── store-globals.blade.php        ← NEW: toast + contact modal + QR modal (~170 baris)
```

**`store.blade.php` setelah refaktor (~90 baris):**

```blade
@php
    // Semua logika deteksi setting, waNumber, storeName, orderTypes
    // tetap di sini — tidak dipindah ke partial
@endphp

{{-- ===== FALLBACK: Toko Tidak Aktif ===== --}}
@if (! $setting)
    @include('layouts._partials.store-not-found')
@else

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-no-progress-bar>
<head>
    @include('layouts._partials.store-head', [
        'setting'   => $setting,
        'storeName' => $storeName,
    ])
</head>

<body>

    {{-- Loader (di luar Alpine agar error tidak mem-block-nya) --}}
    @include('pages.tenant.store.resto.partials._loader')

    {{-- Root Alpine Container --}}
    <div
        class="bg-[var(--background)] min-h-screen ..."
        x-data="storeApp"
        data-default-order-type="{{ $orderTypes[0]['id'] }}"
        data-wa-number="{{ $waNumber }}"
        {{-- ... semua data-* attributes --}}
        @open-qr-modal.window="qrOpen = true"
        {{-- ... semua event listeners --}}
    >
        {{-- Pull to Refresh Indicator --}}
        <div ...> ... </div>

        {{-- Hero --}}
        @include('pages.tenant.store.resto.partials._hero', ['setting' => $setting])

        {{-- Page Content --}}
        {{ $slot }}

        {{-- Global UI: Contact Modal, Toast, QR Modal --}}
        @include('layouts._partials.store-globals', [
            'setting'    => $setting,
            'orderTypes' => $orderTypes,
        ])

        {{-- Domain Modals (Client-Side) --}}
        @include('pages.tenant.store.resto.modals.option-modal')
        @include('pages.tenant.store.resto.modals.checkout-modal', ['orderTypes' => $orderTypes])
        @include('pages.tenant.store.resto.modals.history-modal')

        {{-- AI Chat --}}
        <livewire:components::tenant.ai-floating-chat/>
    </div>

    @livewireScripts
</body>
</html>

@endif
```

---

## Directory Structure After Refactor

```
.docs/references/livewire4/
├── STANDARDS.md                       ← NEW ✨
├── mfc-alpine-architecture.md         (unchanged)
├── advanced/                          (unchanged)
├── blade-directives/                  (unchanged)
├── essentials/                        (unchanged)
├── features/                          (unchanged)
├── getting-started/                   (unchanged)
├── html-directives/                   (unchanged)
└── php-attributes/                    (unchanged)

routes/
└── tenant.php                         ← MODIFY (section grouping)

resources/views/layouts/
├── store.blade.php                    ← MODIFY (~90 baris, dari 370)
└── _partials/
    ├── store-not-found.blade.php      ← NEW
    ├── store-head.blade.php           ← NEW
    └── store-globals.blade.php        ← NEW
```

---

## Verification Plan

### Post-Refactor Checklist

- [ ] `php artisan route:list` — Pastikan semua route masih terdaftar dengan nama yang sama
- [ ] Buka `/` (resto & retail) — Tampilan store normal
- [ ] Buka `/invoice/{code}` dan `/order/{code}` — Render benar
- [ ] Toast, contact modal, QR modal masih berfungsi
- [ ] Pull-to-refresh masih berfungsi di mobile
- [ ] Route auth redirect masih benar
- [ ] AI chat floating button masih muncul

### Commands

```bash
# Cek semua route masih ada
php artisan route:list --path=/ | grep -E "(invoice|order|cashier|dashboard)"

# Cek tidak ada syntax error
php artisan view:clear && php artisan config:clear
```

---

## File References

- [tenant.php](routes/tenant.php)
- [store.blade.php](resources/views/layouts/store.blade.php)
- [HomeController.php](app/Tenant/Controllers/Web/HomeController.php)
- [.docs/references/livewire4/essentials/pages.md](.docs/references/livewire4/essentials/pages.md)
- [.docs/references/livewire4/essentials/components.md](.docs/references/livewire4/essentials/components.md)
- [.docs/references/livewire4/mfc-alpine-architecture.md](.docs/references/livewire4/mfc-alpine-architecture.md)
