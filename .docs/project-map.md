# Project Map — pakaiapp-dash

Dokumen ini adalah referensi arsitektur codebase untuk agen AI dan developer. Dibuat otomatis dari hasil scan proyek pada 2026-07-23. Terakhir diperbarui: 2026-07-27.

---

## Arsitektur

**Type:** Multi-Tenant SaaS (Laravel 13 + Stancl Tenancy)
**Pattern:** Modular per-domain (`Central`, `Tenant`, `Shared`)
**Auth:** Session-based (Livewire pages) + Token-less API
**Realtime:** Laravel Broadcasting (Reverb/Pusher)

---

## Struktur Folder `app/`

```
app/
├── Central/                        # Domain: Admin Pusat / Superadmin
│   ├── Controllers/
│   │   ├── ArticleController.php
│   │   ├── AuthController.php
│   │   ├── DuitkuController.php
│   │   └── MidtransController.php
│   ├── Data/                       # Data Transfer Objects (DTO)
│   │   ├── CentralLoginInputData.php
│   │   ├── CentralLoginResultData.php
│   │   ├── DuitkuInvoiceResultData.php
│   │   ├── DuitkuPaymentMethodData.php
│   │   ├── DuitkuTransactionStatusData.php
│   │   ├── GetPaymentMethodsInputData.php
│   │   ├── RegisterStatusData.php
│   │   ├── RegisterTenantInputData.php
│   │   ├── RegistrationResultData.php
│   │   ├── RequestOtpInputData.php
│   │   └── VerifyOtpInputData.php
│   ├── Http/Middleware/
│   │   └── DuitkuEnabled.php
│   ├── Models/
│   │   ├── Article.php
│   │   ├── GlobalSetting.php
│   │   ├── Quota.php
│   │   ├── Tenant.php
│   │   ├── TenantRegistration.php
│   │   └── User.php
│   └── Services/
│       ├── BillingService.php
│       ├── DuitkuService.php
│       ├── MidtransService.php
│       ├── RegistrationAbuseGuardService.php
│       └── TenantRegistrationService.php
│
├── Console/Commands/               # Artisan Commands
│   ├── CancelExpiredOrders.php
│   ├── CreateTenant.php
│   ├── CreateTenantSymlinks.php
│   ├── MigrateTenantTypeCommand.php
│   ├── RunAiPricingRules.php
│   └── TestDuitkuCallback.php
│
├── Http/Controllers/
│   └── Controller.php              # Base Controller
│
├── Livewire/Forms/
│   └── ProfileForm.php
│
├── Observers/
│   └── ProductObserver.php         # Daftarkan via #[ObservedBy] di model
│
├── Providers/
│   ├── AppServiceProvider.php
│   └── TenancyServiceProvider.php
│
├── Shared/                         # Kode lintas domain (Central & Tenant)
│   ├── Jobs/
│   │   └── CreateFrameworkDirectoriesForTenant.php
│   ├── Listeners/
│   │   └── EnforceSessionLimits.php
│   ├── Mail/
│   │   └── SystemEmail.php
│   ├── Middleware/
│   │   ├── CheckRole.php
│   │   ├── CheckStoreOpen.php              # ← planned (Fase 2 operating-hours)
│   │   ├── FileUrlMiddleware.php
│   │   └── IpWhitelist.php
│   └── Traits/
│       ├── ApiPaginationTrait.php
│       ├── ApiResponserTrait.php
│       ├── ClearsAiMenuCache.php
│       ├── ClearsStoreSettingCache.php     # ← planned (Fase 2 operating-hours)
│       └── ShowsToast.php
│
├── Tenant/                         # Domain: Per-Tenant (Toko)
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── CategoryApiController.php
│   │   │   ├── DuitkuApiController.php
│   │   │   ├── DuitkuCallbackController.php
│   │   │   ├── OrderApiController.php
│   │   │   ├── OrderHistoryApiController.php
│   │   │   ├── ProductApiController.php
│   │   │   └── RestaurantApiController.php
│   │   └── Web/
│   │       ├── CashierController.php
│   │       ├── HomeController.php
│   │       ├── MenuController.php
│   │       └── TenantManifestController.php
│   ├── Data/
│   │   ├── CategoryData.php
│   │   ├── CheckoutData.php
│   │   ├── CreateOrderData.php
│   │   └── ProductFilterData.php
│   ├── Events/
│   │   └── KitchenUpdated.php
│   ├── Models/
│   │   ├── Ai/
│   │   │   ├── AiChatMessage.php
│   │   │   ├── AiChatSession.php
│   │   │   └── AiPricingRule.php
│   │   ├── Core/
│   │   │   ├── Category.php
│   │   │   ├── Order.php
│   │   │   ├── OrderItem.php
│   │   │   ├── Product.php
│   │   │   ├── ProductVariant.php
│   │   │   ├── StoreSetting.php
│   │   │   ├── TenantUser.php
│   │   │   ├── Wallet.php
│   │   │   └── WalletTransaction.php
│   │   └── Resto/                          # Model khusus tenant tipe "resto"
│   │       ├── ProductExtra.php            # ← dipindah dari Core/ (2026-07-27)
│   │       ├── RawMaterial.php
│   │       └── VariantRecipe.php
│   └── Services/
│       ├── CategoryService.php
│       ├── KitchenService.php
│       ├── OpenAiMenuService.php
│       ├── OpenAiSupportService.php
│       ├── OrderService.php
│       ├── PaymentGatewayService.php
│       ├── ProductService.php
│       ├── SettingService.php
│       └── TenantWalletService.php
│
└── View/Components/
    └── AdaptiveStatCard.php
```

---

## Models

### Central Models

| Model | Tabel | Atribut Khusus | Traits/Notes |
|---|---|---|---|
| `Article` | `articles` | — | `casts: published_at → datetime` |
| `User` | `users` | `#[Hidden(['password','remember_token'])]` | `HasFactory`, `Notifiable`, `casts: email_verified_at, password` |
| `Tenant` | `tenants` | `#[Fillable]` | extends `BaseTenant`, `HasDatabase`, `HasDomains` |
| `TenantRegistration` | `tenant_registrations` | `#[Hidden(['password'])]` | — |
| `Quota` | `quotas` | — | `casts: total_slots, used_slots → integer` |
| `GlobalSetting` | `global_settings` | `#[Connection('mysql')]`, `#[Table(key:'key', keyType:'string', incrementing:false)]` | accessor `castValue` |

### Tenant Models — Core

| Model | Tabel | Atribut Khusus | Traits/Notes |
|---|---|---|---|
| `Category` | `categories` | — | — |
| `Product` | `products` | `#[ObservedBy(ProductObserver)]` | `ClearsAiMenuCache`, accessors: `price`, `formattedPrice`, `totalStock`, custom route binding |
| `ProductVariant` | `product_variants` | — | `ClearsAiMenuCache`, accessor `profitMargin` (legacy) |
| `Order` | `orders` | — | accessor `formattedPaymentMethod` (legacy), method `restoreStock()` |
| `OrderItem` | `order_items` | — | — |
| `StoreSetting` | `store_settings` | `operating_hours` (JSON), `use_same_hours` (bool) | `isOpenNow()`, `getTodayHours()`, `cached()` (planned), `ClearsStoreSettingCache` (planned) |
| `TenantUser` | `users` | `#[Table('users')]`, `#[Hidden]` | extends `Authenticatable`, `casts: email_verified_at, password` |
| `Wallet` | `wallets` | — | `casts: balance, monthly_fee_paid → decimal:2` |
| `WalletTransaction` | `wallet_transactions` | — | `casts: amount, opening_balance, closing_balance → decimal:2`, `morphTo` |

### Tenant Models — Ai

| Model | Tabel | Atribut Khusus | Traits/Notes |
|---|---|---|---|
| `AiChatSession` | `ai_chat_sessions` | — | `MassPrunable` (hapus setelah 24 jam via `model:prune`) |
| `AiChatMessage` | `ai_chat_messages` | — | `belongsTo AiChatSession` |
| `AiPricingRule` | `ai_pricing_rules` | — | `casts: active_days → array, is_active → boolean`, `belongsToMany ProductVariant` |

### Tenant Models — Resto

| Model | Tabel | Traits/Notes |
|---|---|---|
| `ProductExtra` | `product_extras` | `ClearsAiMenuCache`, `casts: price, cost → float, is_active → boolean`. Tabel hanya ada di DB tenant tipe `resto`. |
| `RawMaterial` | `raw_materials` | — |
| `VariantRecipe` | `variant_recipes` | `HasFactory`, `belongsTo ProductVariant & RawMaterial` |

---

## Routes

### Central (`routes/web.php`)

Semua route Central dibungkus `Route::domain($domain)->group(...)` per entry di `config('tenancy.central_domains')`.

| Method | URI | Controller / Action | Middleware |
|---|---|---|---|
| GET | `/` | `view: pages.public.welcome` | — |
| GET | `/kasir-cafe` | `view: pages.public.landing-cafe` | — |
| GET | `/kasir-toko-kelontong` | `view: pages.public.landing-retail` | — |
| GET | `/blog` | `ArticleController@index` | — |
| GET | `/blog/{slug}` | `ArticleController@show` | — |
| GET | `/register` | `AuthController@showRegister` | — |
| GET | `/login` | `AuthController@showLogin` | — |
| GET | `/register/status/{invoice_code}` | `AuthController@registerStatus` | — |
| GET | `/api/register/status/{invoice_code}` | `AuthController@apiRegisterStatus` | — |
| POST | `/api/central-login` | `AuthController@centralLogin` | — |
| POST | `/api/request-otp` | `AuthController@requestOtp` | — |
| POST | `/api/verify-otp` | `AuthController@verifyOtp` | — |
| POST | `/api/register-tenant` | `AuthController@registerTenant` | — |
| GET | `/api/duitku/payment-methods` | `DuitkuController@getPaymentMethods` | `DuitkuEnabled` |
| POST | `/duitku/callback` | `DuitkuController@callback` | `IpWhitelist`, `DuitkuEnabled` |
| GET | `/duitku/return` | `DuitkuController@return` | `DuitkuEnabled` |
| GET | `/duitku/status/{invoiceCode}` | `DuitkuController@status` | `DuitkuEnabled` |
| POST | `/midtrans/notification` | `MidtransController@notification` | `IpWhitelist` |

### Tenant (`routes/tenant.php`)

Global middleware: `web`, `InitializeTenancyByDomain`, `PreventAccessFromCentralDomains`, `FileUrlMiddleware`.

| Method | URI | Controller / Livewire | Auth | Role |
|---|---|---|---|---|
| GET | `/` | `HomeController` | Public | — |
| GET | `/manifest.json` | `TenantManifestController` | Public | — |
| GET | `/menu/{product}` | `MenuController@show` | Public | — |
| GET | `/menu/{product}/story` | `MenuController@shareAsStory` | Public | — |
| GET | `/invoice/{code}` | `pages::tenant.invoice.show` | Public | — |
| GET | `/receipt/{code}` | `pages::tenant.receipt.show` | Public | — |
| GET | `/order/{code}` | `pages::tenant.order.show` | Public | — |
| GET | `/cashier` | `CashierController` | Auth | manager, cashier |
| GET | `/order` | `view: pages.tenant.order.index` | Auth | manager, cashier |
| GET | `/profile` | `pages::tenant.profile.user-profile` | Auth | manager, cashier |
| GET | `/menu` | `pages::tenant.mobile-menu` | Auth | manager, cashier |
| GET | `/kitchen` | `pages::tenant.kitchen` | Auth | manager, kitchen |
| GET | `/dashboard` | `pages::tenant.dashboard` | Auth | manager |
| GET | `/ai-engine` | `pages::tenant.ai-engine-manager` | Auth | manager |
| GET | `/wallet` | `pages::tenant.payment.wallet` | Auth | manager |
| GET | `/product` | `view: pages.tenant.product.product` | Auth | manager |
| GET | `/product/create` | `pages::tenant.product.form` | Auth | manager |
| GET | `/product/{product}/edit` | `pages::tenant.product.form` | Auth | manager |
| GET | `/raw-material` | `pages::tenant.resto.raw-material` | Auth | manager |
| GET | `/store-setting` | `pages::tenant.setting.store-setting` | Auth | manager |
| GET | `/product-slot/buy` | `pages::tenant.setting.buy-product-slot` | Auth | manager |
| GET | `/user` | `view: pages.tenant.user.index` | Auth | manager |

### Tenant API (`routes/tenant/api.php`)

Prefix: `/api`, middleware: `api`.

| Method | URI | Controller | Middleware |
|---|---|---|---|
| GET | `/api/restaurant` | `RestaurantApiController` | — |
| POST | `/api/orders` | `OrderApiController@store` | `throttle:orders`, `store.open` (planned) |
| POST | `/api/orders/history` | `OrderHistoryApiController@index` | `throttle:30,1` |
| GET | `/api/duitku/payment-methods` | `DuitkuApiController@getPaymentMethods` | — |

### Auth (`routes/auth.php`)

| Method | URI | Livewire / Action |
|---|---|---|
| GET | `/auth/login` | `pages::auth.login` |
| GET | `/auth/forgot-password` | `pages::auth.forgot-password` |
| GET | `/auth/reset-password` | `pages::auth.reset-password` |
| GET | `/auth/auto-login` | Closure (token → Auth::login) |

---

## Middleware

| Alias / Class | Lokasi | Fungsi |
|---|---|---|
| `role` | `app/Shared/Middleware/CheckRole.php` | Cek `$user->role` vs allowed roles |
| `store.open` | `app/Shared/Middleware/CheckStoreOpen.php` | Blokir order jika di luar jam operasional (planned, Fase 2) |
| — | `app/Shared/Middleware/FileUrlMiddleware.php` | Set URL disk `public` sesuai tenant ID |
| — | `app/Shared/Middleware/IpWhitelist.php` | Whitelist IP Duitku & Midtrans untuk webhook |
| — | `app/Central/Http/Middleware/DuitkuEnabled.php` | Guard jika Duitku dinonaktifkan via config |

Registrasi di `bootstrap/app.php`:
- `role` → alias ke `CheckRole`
- CSRF exception: `duitku/callback`, `midtrans/notification`
- Trust Proxies: `*`
- Guest redirect: `/auth/login`

---

## Services

### Central Services

| Service | Tanggung Jawab |
|---|---|
| `BillingService` | Logika billing/langganan tenant |
| `DuitkuService` | Integrasi Duitku payment gateway |
| `MidtransService` | Integrasi Midtrans payment gateway |
| `RegistrationAbuseGuardService` | Deteksi abuse saat registrasi |
| `TenantRegistrationService` | Orkestrasi proses daftar tenant baru |

### Tenant Services

| Service | Tanggung Jawab |
|---|---|
| `CategoryService` | CRUD & validasi kategori produk |
| `KitchenService` | Logika status dapur (KDS) |
| `OpenAiMenuService` | AI menu recommendation (OpenAI) |
| `OpenAiSupportService` | AI chat support (OpenAI) |
| `OrderService` | Pembuatan & manajemen order |
| `PaymentGatewayService` | Abstraksi Duitku/Midtrans di tenant |
| `ProductService` | Filter, sort, & bulk action produk |
| `SettingService` | Ambil setting tenant dari cache/DB |
| `TenantWalletService` | Manajemen saldo & transaksi wallet |

---

## Traits (`app/Shared/Traits/`)

| Trait | Digunakan di | Fungsi |
|---|---|---|
| `ClearsAiMenuCache` | `Product`, `ProductVariant`, `ProductExtra` | Hapus cache AI menu saat model `saved`/`deleted` |
| `ClearsStoreSettingCache` | `StoreSetting` | Hapus cache setting toko saat model `saved`/`deleted` (planned, Fase 2) |
| `ShowsToast` | Livewire components | Helper `toast()` kirim JS event ke frontend |
| `ApiResponserTrait` | API Controllers | Standarisasi response JSON (`successResponse`, `failResponse`, `errorResponse`) |
| `ApiPaginationTrait` | API Controllers | Wrapper pagination manual & otomatis |

---

## Events & Listeners

| Event | Listener | Registrasi |
|---|---|---|
| `Illuminate\Auth\Events\Login` | `App\Shared\Listeners\EnforceSessionLimits` | Manual di `AppServiceProvider::boot()` |
| `App\Tenant\Events\KitchenUpdated` | — (Broadcasting) | Di-broadcast via channel |

> **Note:** `EnforceSessionLimits` ada di `app/Shared/Listeners/` (bukan default `app/Listeners/`), sehingga auto-discovery tidak menemukannya. Didaftarkan manual di `AppServiceProvider`.

---

## Observers

| Observer | Model | Registrasi | Fungsi |
|---|---|---|---|
| `ProductObserver` | `Product` | `#[ObservedBy(ProductObserver::class)]` | `creating`: cek quota slot produk. `created`: increment quota. `deleted`: decrement quota. |

---

## Scheduled Tasks (`routes/console.php`)

| Command | Frekuensi | Options |
|---|---|---|
| `pakaiapp:run-ai-pricing` | Every minute | `withoutOverlapping()` |
| `orders:cancel-expired` | Every minute | `withoutOverlapping()` |
| `tenants:run model:prune --option=model=AiChatSession` | Daily | — |

---

## Artisan Commands (`app/Console/Commands/`)

| Command | Class | Fungsi |
|---|---|---|
| `orders:cancel-expired` | `CancelExpiredOrders` | Batalkan order yang sudah expired |
| `tenants:create` | `CreateTenant` | Buat tenant baru via CLI |
| `tenants:symlinks` | `CreateTenantSymlinks` | Buat symlink storage per tenant |
| `tenants:migrate-type` | `MigrateTenantTypeCommand` | Migrasi tipe toko (resto/retail) |
| `pakaiapp:run-ai-pricing` | `RunAiPricingRules` | Jalankan pricing rule AI per tenant |
| `duitku:test-callback` | `TestDuitkuCallback` | Test simulasi callback Duitku |

---

## Bootstrap (`bootstrap/app.php`)

```php
Application::configure()
    ->withRouting(web, commands, channels, health: '/up')
    ->withMiddleware(...)   // alias, trustProxies, CSRF exceptions
    ->withExceptions(...)  // dontReport TenantCouldNotBeIdentifiedOnDomainException
    ->create()
```

---

## Rate Limiters

| Name | Logic |
|---|---|
| `orders` | Guest: max 5/menit per IP. Auth user: unlimited. |

Didefinisikan di `AppServiceProvider::boot()`.

---

## Known Tech Debt (per 2026-07-27)

| # | File | Issue |
|---|---|---|
| 1 | `Order.php` | `getFormattedPaymentMethodAttribute()` — legacy accessor, belum migrasi ke `Attribute::make()` |
| 2 | `ProductVariant.php` | `getProfitMarginAttribute()` — legacy accessor, belum migrasi ke `Attribute::make()` |
| 3 | `GlobalSetting.php` | `getCastValueAttribute()` — legacy accessor, belum migrasi ke `Attribute::make()` |
| 4 | `User.php`, `TenantUser.php` | Docblock `@return array<string, string>` di atas `casts()` — redundant |
| 5 | `routes/console.php` | Schedule tasks belum pakai `onOneServer()` — perlu jika multi-server |
| 6 | `AppServiceProvider.php` | `Event::listen` manual — bisa diganti auto-discovery via `withEvents` di `bootstrap/app.php` |
| 7 | `routes/tenant.php` | `/raw-material` dan `/kitchen` tidak ada route guard per `store_type` — accessible oleh semua tenant types via URL langsung |
| 8 | `RestaurantApiController.php` | `StoreSetting::first()` tanpa cache — akan diperbaiki di Fase 2 operating-hours |
| 9 | `MenuController.php` | Halaman detail produk (`/menu/{slug}`) tidak melewati gate `is_active` / `isOpenNow()` — akan diperbaiki di Fase 3 operating-hours |

---

## Architecture Decisions

| Doc | Decision |
|-----|----------|
| [`decisions/001-service-dto-pattern.md`](decisions/001-service-dto-pattern.md) | Standarisasi Service + DTO untuk semua business logic baru |

## Features

| Doc | Status | Deskripsi |
|-----|--------|-----------|
| [`features/operating-hours/plan.md`](features/operating-hours/plan.md) | ✅ Shipped | Jam operasional toko: jadwal per hari, middleware cek order, caching StoreSetting, UI storefront, UI Settings Tailwind |
| [`features/store-setting-ui/plan.md`](features/store-setting-ui/plan.md) | ✅ Shipped | Rewrite halaman pengaturan toko ke Tailwind + tambah section Jam Operasional |
| [`features/ai-menu-engine/`](features/ai-menu-engine/) | ✅ Shipped | AI menu recommendation via OpenAI |
| [`features/pos-queue-optimization/`](features/pos-queue-optimization/) | ✅ Shipped | Optimasi antrian POS dapur |
| [`features/tailwind-migration/`](features/tailwind-migration/) | 🔄 In Progress | Migrasi Bootstrap → Tailwind CSS |
| [`features/ui-standards/`](features/ui-standards/) | ✅ Shipped | Standar UI komponen |

## Additional References

| Doc | Description |
|-----|-------------|
| [`references/livewire4/alpine-morph-resolution.md`](references/livewire4/alpine-morph-resolution.md) | Solusi Alpine.js race condition dengan Livewire DOM morphing |
