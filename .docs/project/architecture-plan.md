# Pakaiapp Dash — Architecture Restructuring Plan

> Target: Merapikan struktur folder, namespace, views, routes, dan config.
> Total estimasi: **7–11 jam**, dibagi dalam **4 phase** independen.

---

## Phase 1: Housekeeping (1–2 jam)

**Tujuan:** Buang sampah, pindahkan dokumentasi vendor ke luar project.

### Delete

| Path                                       | Alasan                                                        |
|--------------------------------------------|---------------------------------------------------------------|
| `resources/sass/`                          | SCSS tidak dikonsumsi Vite (pakai PostCSS + Tailwind)         |
| `docs/`                                    | 26 file dokumentasi Midtrans — ini vendor docs, bukan project |
| `livewire4docs/`                           | Dokumentasi Livewire 4 tercampur di root                      |
| `pakaiapp_central`                         | Binary/symlink tidak dikenal di root                          |
| `database/migrations/tenant/retail/`       | Folder kosong — belum ada fitur retail                        |
| `app/View/Components/AdaptiveStatCard.php` | Class kosong, tidak dipakai sebagai Laravel view component    |

### Move

| From            | To                    | Alasan                               |
|-----------------|-----------------------|--------------------------------------|
| `core-feature/` | `.docs/core-feature/` | PRD & spec — dokumentasi, bukan kode |

### Hasil

Root bersih. Hanya tersisa kode aplikasi, config, dan asset.

---

## Phase 2: Domain Separation — Central vs Tenant (3–4 jam)

**Tujuan:** Pisahkan kode yang berjalan di domain Central (SaaS) dan Tenant (operasional toko).

### Struktur Target

```
app/
├── Central/
│   ├── Controllers/
│   │   ├── AuthController.php         # ex CentralAuthController
│   │   ├── DuitkuController.php       # ex CentralDuitkuController
│   │   └── MidtransController.php     # ex CentralMidtransController
│   ├── Models/
│   │   ├── Tenant.php
│   │   ├── TenantRegistration.php
│   │   ├── User.php
│   │   ├── Article.php
│   │   ├── GlobalSetting.php
│   │   └── Quota.php
│   └── Services/
│       ├── DuitkuService.php
│       ├── MidtransService.php
│       └── BillingService.php
│
├── Tenant/
│   ├── Controllers/
│   │   ├── Web/
│   │   │   ├── HomeController.php
│   │   │   ├── MenuController.php
│   │   │   └── TenantManifestController.php
│   │   └── Api/
│   │       ├── OrderApiController.php
│   │       ├── OrderHistoryApiController.php
│   │       ├── RestaurantApiController.php
│   │       ├── DuitkuApiController.php
│   │       ├── CategoryApiController.php
│   │       └── ProductApiController.php
│   ├── Models/
│   │   ├── Core/
│   │   │   ├── StoreSetting.php
│   │   │   ├── Category.php
│   │   │   ├── Product.php
│   │   │   ├── ProductVariant.php
│   │   │   ├── ProductExtra.php
│   │   │   ├── Order.php
│   │   │   ├── OrderItem.php
│   │   │   ├── Wallet.php
│   │   │   └── WalletTransaction.php
│   │   ├── Resto/
│   │   │   ├── RawMaterial.php
│   │   │   └── VariantRecipe.php
│   │   └── Ai/
│   │       ├── AiChatSession.php
│   │       ├── AiChatMessage.php
│   │       └── AiPricingRule.php
│   └── Services/
│       ├── OrderService.php
│       ├── SettingService.php
│       └── TenantWalletService.php
│
├── Shared/
│   ├── Traits/
│   │   ├── ApiPaginationTrait.php
│   │   ├── ApiResponserTrait.php
│   │   └── ClearsAiMenuCache.php
│   ├── Middleware/
│   │   ├── CheckRole.php
│   │   ├── FileUrlMiddleware.php
│   │   └── IpWhitelist.php           # gabung Duitku + Midtrans
│   ├── Mail/
│   │   └── SystemEmail.php
│   ├── Jobs/
│   │   └── CreateFrameworkDirectoriesForTenant.php
│   └── Listeners/
│       └── EnforceSessionLimits.php
│
└── Providers/
    ├── AppServiceProvider.php
    └── TenancyServiceProvider.php
```

### Langkah

1. Buat folder `app/Central/`, `app/Tenant/`, `app/Shared/` dengan subfolder
2. Pindahkan file satu per satu, update `namespace`
3. Update semua `use` import di seluruh project
4. Update `composer.json` PSR-4 autoload:
   ```json
   "autoload": {
       "psr-4": {
           "App\\": "app/",
           "App\\Central\\": "app/Central/",
           "App\\Tenant\\": "app/Tenant/",
           "App\\Shared\\": "app/Shared/"
       }
   }
   ```
5. Jalankan `composer dump-autoload`
6. Jalankan `php artisan optimize:clear`
7. Test: `php artisan test` (jika ada) atau akses halaman utama

### Catatan

- `App\\` namespace tetap ada di root `app/` untuk `Providers/` dan `Console/`
- Model yang scope-nya tenant TIDAK BOLEH diakses dari Central context (dan sebaliknya)
- Update `config/auth.php` jika providers.guards merujuk ke model yang dipindahkan

---

## Phase 3: View & Asset Restructuring (2–3 jam)

**Tujuan:** Konsolidasi views dengan struktur jelas, hilangkan dead code.

### Struktur Target Views

```
resources/views/
├── central/
│   ├── layouts/
│   │   └── central.blade.php
│   ├── welcome.blade.php
│   ├── register.blade.php
│   ├── register-status.blade.php
│   ├── login.blade.php
│   └── pages/
│       ├── blog/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── landing-cafe.blade.php
│       └── landing-retail.blade.php
│
├── tenant/
│   ├── layouts/
│   │   ├── store.blade.php           # customer-facing QR menu
│   │   ├── app.blade.php             # admin dashboard shell
│   │   ├── print.blade.php
│   │   ├── guest.blade.php
│   │   ├── retail.blade.php
│   │   └── mobile.blade.php
│   │
│   ├── store/                        # Customer-facing QR menu
│   │   ├── resto/
│   │   │   ├── index.blade.php
│   │   │   ├── _hero.blade.php
│   │   │   ├── _loader.blade.php
│   │   │   ├── option-modal.blade.php
│   │   │   ├── checkout-modal.blade.php
│   │   │   └── history-modal.blade.php
│   │   └── retail/
│   │       └── index.blade.php
│   │
│   ├── admin/                        # Dashboard + management
│   │   ├── dashboard/
│   │   ├── pos/
│   │   ├── kitchen/
│   │   ├── product/
│   │   ├── order/
│   │   ├── ai-engine/
│   │   ├── raw-material/
│   │   ├── wallet/
│   │   ├── user/
│   │   ├── setting/
│   │   └── profile/
│   │
│   ├── auth/
│   │   ├── login.blade.php
│   │   ├── forgot-password.blade.php
│   │   └── reset-password.blade.php
│   │
│   └── components/                   # Blade UI components
│       ├── ui/
│       │   ├── stat-card.blade.php
│       │   └── adaptive-stat-card.blade.php
│       └── tenant/
│           ├── ai-floating-chat.blade.php
│           ├── navbar.blade.php
│           ├── sidebar.blade.php
│           ├── sidebar-item.blade.php
│           └── tour-guide.blade.php
│
├── emails/
└── errors/
```

### Langkah

1. Rename semua folder `⚡xxx` → `xxx` (hapus prefix petir)
2. Update referensi di setiap Livewire component:
    - Class: `#[Layout('components.layouts.app')]` atau `#[Title('Dashboard')]`
    - Route: `Route::livewire('dashboard', 'pages::tenant.dashboard')` → sesuaikan namespace
3. Gabungkan komponen duplikat:
    - `components/desktop/stat-card.blade.php` + `components/mobile/stat-card.blade.php`
      → `components/ui/stat-card.blade.php` (pakai responsive class)
4. Pindahkan `components/layouts/` → `tenant/layouts/` + `central/layouts/`
5. Rapikan `components/pages/` → gabung ke `tenant/admin/` atau `central/pages/`
6. Pastikan semua `@include`, `@extends`, `view()` reference ter-update

### Asset Cleanup

| File                        | Aksi                          |
|-----------------------------|-------------------------------|
| `resources/sass/`           | HAPUS (Phase 1)               |
| `resources/css/app.css`     | Tetap — dashboard/admin       |
| `resources/css/store.css`   | Tetap — customer-facing store |
| `resources/css/welcome.css` | Tetap — landing page          |
| `resources/js/app.js`       | Tetap                         |
| `resources/js/store.js`     | Tetap                         |
| `resources/js/welcome.js`   | Tetap                         |

---

## Phase 4: Route & Config Cleanup (1–2 jam)

**Tujuan:** Ekstrak closure, pisah API route, bersihkan config.

### Route Restructuring

```
routes/
├── web.php              # Central domain (landing, register, payment callback)
├── tenant.php           # Tenant web routes (POS, dashboard, kitchen)
├── tenant-api.php       # Tenant API routes  <-- BARU, dipisah dari tenant.php
├── auth.php             # Shared auth routes
└── console.php          # Artisan commands + schedule
```

### Perubahan di `routes/web.php`

Ekstrak closure ke controller:

```php
// SEBELUM:
Route::get('/api/duitku/payment-methods', function (Request $request) {
    // ... closure logic
});

// SESUDAH:
Route::get('/api/duitku/payment-methods', [CentralDuitkuController::class, 'paymentMethods']);
```

### Perubahan di `routes/tenant.php`

Pisahkan API routes ke `routes/tenant-api.php`:

```php
// routes/tenant.php — hanya web routes
Route::middleware([...])->group(function () {
    Route::get('/', HomeController::class);
    Route::controller(MenuController::class)->prefix('menu')...
    Route::middleware('auth')->group(function () { ... });
    require __DIR__ . '/auth.php';
});

// routes/tenant-api.php — hanya API routes
Route::middleware([...])->prefix('api')->group(function () {
    Route::get('/restaurant', RestaurantApiController::class);
    Route::prefix('orders')->group(function () { ... });
    Route::prefix('duitku')->group(function () { ... });
});
```

### Config Cleanup

| Config                | Perubahan                                               |
|-----------------------|---------------------------------------------------------|
| `config/duitku.php`   | Pindahkan `merchant_code`, `api_key` hardcoded → `.env` |
| `config/midtrans.php` | Pindahkan `server_key`, `client_key` hardcoded → `.env` |
| `config/services.php` | Tambahkan `openai.key` → `.env`                         |
| `config/tenancy.php`  | Review `central_domains` — pastikan index `[2]` valid   |
| `config/auth.php`     | Update model reference setelah Phase 2                  |

### Hapus Controller Tak Terpakai

| File                                                    | Alasan                                |
|---------------------------------------------------------|---------------------------------------|
| `app/Http/Controllers/Api/DuitkuCallbackController.php` | Tidak direferensikan di route manapun |

---

## Migration Structure (Bonus)

Setelah Phase 2, rapikan migrasi mengikuti domain:

```
database/migrations/
├── central/                         # Central DB
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2019_09_15_000010_create_tenants_table.php
│   ├── 2019_09_15_000020_create_domains_table.php
│   ├── 2026_05_17_102958_add_store_type_to_tenants_table.php
│   ├── 2026_05_25_000002_create_global_settings_table.php
│   ├── 2026_05_25_000003_create_tenant_registrations_table.php
│   ├── ...
│   └── 2026_06_12_171100_create_articles_table.php
│
└── tenant/
    ├── core/                        # Dipakai resto & retail
    │   ├── 0001_01_01_000000_create_users_table.php
    │   ├── ...
    │   ├── 2026_04_24_140650_create_products_table.php
    │   ├── 2026_04_24_141029_create_orders_table.php
    │   ├── ...
    │   └── 2026_06_24_031309_add_customer_email_to_orders_table.php
    │
    └── resto/                       # F&B specific
        ├── 2026_04_26_073213_create_product_extras_table.php
        ├── 2026_05_16_234859_add_selection_rules_to_products_table.php
        └── 2026_06_03_152337_create_raw_materials_and_recipes_tables.php
```

Perubahan path migrasi harus diupdate di `TenancyServiceProvider` atau config tenancy.

---

## Rangkuman Eksekusi

### Urutan yang Direkomendasikan

```
Phase 1  →  Housekeeping
              Commit: "chore: housekeeping - remove unused files and vendor docs"

Phase 2  →  Domain Separation
              Commit: "refactor: separate Central and Tenant domains"

Phase 3  →  View & Asset Restructuring
              Commit: "refactor: restructure views"

Phase 4  →  Route & Config Cleanup
              Commit: "refactor: extract route closures, separate API routes"
```

### Checklist per Phase

- [ ] Phase 1: `git status` bersih, `php artisan optimize:clear` ok
- [ ] Phase 2: `composer dump-autoload` ok, semua halaman masih bisa diakses
- [ ] Phase 3: tidak ada broken `@include`/`@extends`, Livewire component jalan
- [ ] Phase 4: `php artisan route:list` konsisten, tidak ada duplicate route name

### Catatan Penting

- **Phase 2 paling berisiko** karena rename namespace. Kerjakan di branch terpisah.
- Jangan satukan Phase 2 + Phase 3 dalam satu commit — susah di-review.
- Setiap Phase bisa di-merge dan dites mandiri sebelum lanjut ke Phase berikutnya.
- Jalankan `pint` (PHP CS Fixer) setelah selesai setiap phase: `composer lint`
