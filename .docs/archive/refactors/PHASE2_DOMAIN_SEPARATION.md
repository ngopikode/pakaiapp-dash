# Phase 2: Domain Separation — Central vs Tenant

> **Prasyarat:** Phase 1 (Housekeeping) sudah selesai.
> **Referensi:** Lihat [Architecture Plan](.docs/project/architecture-plan.md) untuk gambaran besar.
> **Estimasi:** 3–4 jam
> **Risiko:** Tinggi (rename namespace menyentuh ~78 file)

---

## 1. Tujuan

Memisahkan kode yang berjalan di **domain Central** (SaaS: registrasi, billing, admin pusat) dari kode yang berjalan di **domain Tenant** (operasional toko: POS, produk, order, AI).

Setelah selesai:
- Model, Controller, Service terorganisir per domain
- Namespace jelas: `App\Central\`, `App\Tenant\`, `App\Shared\`
- Tidak ada kode Central yang bocor ke context Tenant (dan sebaliknya)

---

## 2. Struktur Target

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
│   │   ├── User.php                   # central user (admin SaaS)
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
├── Observers/
│   └── ProductObserver.php
│
├── Livewire/
│   └── Forms/
│       └── ProfileForm.php
│
├── Console/
│   └── Commands/
│       ├── CreateTenant.php
│       ├── MigrateTenantTypeCommand.php
│       ├── RunAiPricingRules.php
│       └── TestDuitkuCallback.php
│
└── Providers/
    ├── AppServiceProvider.php
    └── TenancyServiceProvider.php
```

---

## 3. Mapping Pemindahan File

### 3.1 Models (21 file)

| Dari | Ke | Namespace Baru |
|------|-----|----------------|
| `app/Models/Tenant.php` | `app/Central/Models/Tenant.php` | `App\Central\Models\Tenant` |
| `app/Models/TenantRegistration.php` | `app/Central/Models/TenantRegistration.php` | `App\Central\Models\TenantRegistration` |
| `app/Models/User.php` | `app/Central/Models/User.php` | `App\Central\Models\User` |
| `app/Models/Article.php` | `app/Central/Models/Article.php` | `App\Central\Models\Article` |
| `app/Models/GlobalSetting.php` | `app/Central/Models/GlobalSetting.php` | `App\Central\Models\GlobalSetting` |
| `app/Models/Quota.php` | `app/Central/Models/Quota.php` | `App\Central\Models\Quota` |
| `app/Models/StoreSetting.php` | `app/Tenant/Models/Core/StoreSetting.php` | `App\Tenant\Models\Core\StoreSetting` |
| `app/Models/Category.php` | `app/Tenant/Models/Core/Category.php` | `App\Tenant\Models\Core\Category` |
| `app/Models/Product.php` | `app/Tenant/Models/Core/Product.php` | `App\Tenant\Models\Core\Product` |
| `app/Models/ProductVariant.php` | `app/Tenant/Models/Core/ProductVariant.php` | `App\Tenant\Models\Core\ProductVariant` |
| `app/Models/ProductExtra.php` | `app/Tenant/Models/Core/ProductExtra.php` | `App\Tenant\Models\Core\ProductExtra` |
| `app/Models/Order.php` | `app/Tenant/Models/Core/Order.php` | `App\Tenant\Models\Core\Order` |
| `app/Models/OrderItem.php` | `app/Tenant/Models/Core/OrderItem.php` | `App\Tenant\Models\Core\OrderItem` |
| `app/Models/Wallet.php` | `app/Tenant/Models/Core/Wallet.php` | `App\Tenant\Models\Core\Wallet` |
| `app/Models/WalletTransaction.php` | `app/Tenant/Models/Core/WalletTransaction.php` | `App\Tenant\Models\Core\WalletTransaction` |
| `app/Models/TenantUser.php` | `app/Tenant/Models/Core/TenantUser.php` | `App\Tenant\Models\Core\TenantUser` |
| `app/Models/RawMaterial.php` | `app/Tenant/Models/Resto/RawMaterial.php` | `App\Tenant\Models\Resto\RawMaterial` |
| `app/Models/VariantRecipe.php` | `app/Tenant/Models/Resto/VariantRecipe.php` | `App\Tenant\Models\Resto\VariantRecipe` |
| `app/Models/AiChatSession.php` | `app/Tenant/Models/Ai/AiChatSession.php` | `App\Tenant\Models\Ai\AiChatSession` |
| `app/Models/AiChatMessage.php` | `app/Tenant/Models/Ai/AiChatMessage.php` | `App\Tenant\Models\Ai\AiChatMessage` |
| `app/Models/AiPricingRule.php` | `app/Tenant/Models/Ai/AiPricingRule.php` | `App\Tenant\Models\Ai\AiPricingRule` |

### 3.2 Controllers (14 file)

| Dari | Ke | Namespace Baru |
|------|-----|----------------|
| `app/Http/Controllers/CentralAuthController.php` | `app/Central/Controllers/AuthController.php` | `App\Central\Controllers\AuthController` |
| `app/Http/Controllers/CentralDuitkuController.php` | `app/Central/Controllers/DuitkuController.php` | `App\Central\Controllers\DuitkuController` |
| `app/Http/Controllers/CentralMidtransController.php` | `app/Central/Controllers/MidtransController.php` | `App\Central\Controllers\MidtransController` |
| `app/Http/Controllers/ArticleController.php` | `app/Central/Controllers/ArticleController.php` | `App\Central\Controllers\ArticleController` |
| `app/Http/Controllers/HomeController.php` | `app/Tenant/Controllers/Web/HomeController.php` | `App\Tenant\Controllers\Web\HomeController` |
| `app/Http/Controllers/MenuController.php` | `app/Tenant/Controllers/Web/MenuController.php` | `App\Tenant\Controllers\Web\MenuController` |
| `app/Http/Controllers/TenantManifestController.php` | `app/Tenant/Controllers/Web/TenantManifestController.php` | `App\Tenant\Controllers\Web\TenantManifestController` |
| `app/Http/Controllers/Api/OrderApiController.php` | `app/Tenant/Controllers/Api/OrderApiController.php` | `App\Tenant\Controllers\Api\OrderApiController` |
| `app/Http/Controllers/Api/OrderHistoryApiController.php` | `app/Tenant/Controllers/Api/OrderHistoryApiController.php` | `App\Tenant\Controllers\Api\OrderHistoryApiController` |
| `app/Http/Controllers/Api/RestaurantApiController.php` | `app/Tenant/Controllers/Api/RestaurantApiController.php` | `App\Tenant\Controllers\Api\RestaurantApiController` |
| `app/Http/Controllers/Api/DuitkuApiController.php` | `app/Tenant/Controllers/Api/DuitkuApiController.php` | `App\Tenant\Controllers\Api\DuitkuApiController` |
| `app/Http/Controllers/Api/CategoryApiController.php` | `app/Tenant/Controllers/Api/CategoryApiController.php` | `App\Tenant\Controllers\Api\CategoryApiController` |
| `app/Http/Controllers/Api/ProductApiController.php` | `app/Tenant/Controllers/Api/ProductApiController.php` | `App\Tenant\Controllers\Api\ProductApiController` |
| `app/Http/Controllers/Api/DuitkuCallbackController.php` | `app/Tenant/Controllers/Api/DuitkuCallbackController.php` | `App\Tenant\Controllers\Api\DuitkuCallbackController` |

> **Catatan:** `Controller.php` (base class) tetap di `app/Http/Controllers/Controller.php` karena di-extend oleh semua controller.

### 3.3 Services (8 file)

| Dari | Ke | Namespace Baru |
|------|-----|----------------|
| `app/Services/DuitkuService.php` | `app/Central/Services/DuitkuService.php` | `App\Central\Services\DuitkuService` |
| `app/Services/MidtransService.php` | `app/Central/Services/MidtransService.php` | `App\Central\Services\MidtransService` |
| `app/Services/BillingService.php` | `app/Central/Services/BillingService.php` | `App\Central\Services\BillingService` |
| `app/Services/OrderService.php` | `app/Tenant/Services/OrderService.php` | `App\Tenant\Services\OrderService` |
| `app/Services/SettingService.php` | `app/Tenant/Services/SettingService.php` | `App\Tenant\Services\SettingService` |
| `app/Services/TenantWalletService.php` | `app/Tenant/Services/TenantWalletService.php` | `App\Tenant\Services\TenantWalletService` |
| `app/Services/OpenAiMenuService.php` | `app/Tenant/Services/OpenAiMenuService.php` | `App\Tenant\Services\OpenAiMenuService` |
| `app/Services/OpenAiSupportService.php` | `app/Tenant/Services/OpenAiSupportService.php` | `App\Tenant\Services\OpenAiSupportService` |

### 3.4 Shared (10 file)

| Dari | Ke | Namespace Baru |
|------|-----|----------------|
| `app/Traits/ApiPaginationTrait.php` | `app/Shared/Traits/ApiPaginationTrait.php` | `App\Shared\Traits\ApiPaginationTrait` |
| `app/Traits/ApiResponserTrait.php` | `app/Shared/Traits/ApiResponserTrait.php` | `App\Shared\Traits\ApiResponserTrait` |
| `app/Traits/ClearsAiMenuCache.php` | `app/Shared/Traits/ClearsAiMenuCache.php` | `App\Shared\Traits\ClearsAiMenuCache` |
| `app/Http/Middleware/CheckRole.php` | `app/Shared/Middleware/CheckRole.php` | `App\Shared\Middleware\CheckRole` |
| `app/Http/Middleware/FileUrlMiddleware.php` | `app/Shared/Middleware/FileUrlMiddleware.php` | `App\Shared\Middleware\FileUrlMiddleware` |
| `app/Http/Middleware/DuitkuIpWhitelist.php` | `app/Shared/Middleware/IpWhitelist.php` | `App\Shared\Middleware\IpWhitelist` |
| `app/Http/Middleware/MidtransIpWhitelist.php` | *(merge ke IpWhitelist.php)* | `App\Shared\Middleware\IpWhitelist` |
| `app/Mail/SystemEmail.php` | `app/Shared/Mail/SystemEmail.php` | `App\Shared\Mail\SystemEmail` |
| `app/Jobs/CreateFrameworkDirectoriesForTenant.php` | `app/Shared/Jobs/CreateFrameworkDirectoriesForTenant.php` | `App\Shared\Jobs\CreateFrameworkDirectoriesForTenant` |
| `app/Listeners/EnforceSessionLimits.php` | `app/Shared/Listeners/EnforceSessionLimits.php` | `App\Shared\Listeners\EnforceSessionLimits` |

### 3.5 Tetap di Tempat (tidak dipindah)

| File | Alasan |
|------|--------|
| `app/Providers/AppServiceProvider.php` | Laravel convention |
| `app/Providers/TenancyServiceProvider.php` | Laravel convention |
| `app/Console/Commands/*` | Laravel convention |
| `app/Http/Controllers/Controller.php` | Base class, di-extend semua |
| `app/Livewire/Forms/ProfileForm.php` | Livewire convention |
| `app/Observers/ProductObserver.php` | Tetap, tapi update namespace import |

---

## 4. File yang Perlu Update Import (~78 file)

### 4.1 Config (3 file)

| File | Perubahan |
|------|-----------|
| `config/auth.php` | `use App\Models\User` → `use App\Central\Models\User` |
| `config/tenancy.php` | `use App\Models\Tenant` → `use App\Central\Models\Tenant` |
| `config/livewire.php` | Tidak berubah (Livewire namespace tetap `App\Livewire`) |

### 4.2 Routes (4 file)

| File | Perubahan |
|------|-----------|
| `routes/web.php` | Update semua `use App\Http\Controllers\Central*` → `App\Central\Controllers\*` |
| `routes/tenant.php` | Update semua `use App\Http\Controllers\*` → `App\Tenant\Controllers\*` |
| `routes/auth.php` | `\App\Models\User` → `\App\Central\Models\User` |
| `routes/console.php` | `use App\Models\AiChatSession` → `use App\Tenant\Models\Ai\AiChatSession` |

### 4.3 Bootstrap (1 file)

| File | Perubahan |
|------|-----------|
| `bootstrap/app.php` | `App\Http\Middleware\CheckRole` → `App\Shared\Middleware\CheckRole` |

### 4.4 Database (4 file)

| File | Perubahan |
|------|-----------|
| `database/factories/UserFactory.php` | `use App\Models\User` → `use App\Central\Models\User` |
| `database/seeders/DatabaseSeeder.php` | `use App\Models\User` → `use App\Central\Models\User` |
| `database/seeders/MigrationSeeder.php` | Update `Tenant`, `User`, `StoreSetting`, `Category`, `Product` |
| `database/seeders/ArticleSeeder.php` | `\App\Models\Article` → `\App\Central\Models\Article` |

### 4.5 Tests (1 file)

| File | Perubahan |
|------|-----------|
| `tests/Feature/DuitkuIntegrationTest.php` | `use App\Models\Order` → `use App\Tenant\Models\Core\Order` |

### 4.6 Views / Livewire Components (~38 file)

Ini yang paling banyak. Setiap file `.blade.php` atau `.php` di `resources/views/` yang punya `use App\Models\...` atau `use App\Services\...` harus diupdate.

**Model references di views:**

| Model Lama | Model Baru |
|------------|-----------|
| `App\Models\StoreSetting` | `App\Tenant\Models\Core\StoreSetting` |
| `App\Models\Product` | `App\Tenant\Models\Core\Product` |
| `App\Models\ProductVariant` | `App\Tenant\Models\Core\ProductVariant` |
| `App\Models\ProductExtra` | `App\Tenant\Models\Core\ProductExtra` |
| `App\Models\Order` | `App\Tenant\Models\Core\Order` |
| `App\Models\OrderItem` | `App\Tenant\Models\Core\OrderItem` |
| `App\Models\Category` | `App\Tenant\Models\Core\Category` |
| `App\Models\TenantUser` | `App\Tenant\Models\Core\TenantUser` |
| `App\Models\WalletTransaction` | `App\Tenant\Models\Core\WalletTransaction` |
| `App\Models\AiChatSession` | `App\Tenant\Models\Ai\AiChatSession` |
| `App\Models\AiPricingRule` | `App\Tenant\Models\Ai\AiPricingRule` |
| `App\Models\RawMaterial` | `App\Tenant\Models\Resto\RawMaterial` |
| `App\Models\Quota` | `App\Central\Models\Quota` |
| `App\Models\GlobalSetting` | `App\Central\Models\GlobalSetting` |
| `App\Models\Tenant` | `App\Central\Models\Tenant` |
| `App\Models\User` | `App\Central\Models\User` |
| `App\Models\TenantRegistration` | `App\Central\Models\TenantRegistration` |

**Service references di views:**

| Service Lama | Service Baru |
|-------------|-------------|
| `App\Services\OpenAiMenuService` | `App\Tenant\Services\OpenAiMenuService` |
| `App\Services\OpenAiSupportService` | `App\Tenant\Services\OpenAiSupportService` |
| `App\Services\TenantWalletService` | `App\Tenant\Services\TenantWalletService` |
| `App\Services\SettingService` | `App\Tenant\Services\SettingService` |
| `App\Services\BillingService` | `App\Central\Services\BillingService` |
| `App\Services\DuitkuService` | `App\Central\Services\DuitkuService` |

**Mail references di views:**

| Mail Lama | Mail Baru |
|-----------|----------|
| `App\Mail\SystemEmail` | `App\Shared\Mail\SystemEmail` |

**Trait references (inline di model bodies):**

| Trait Lama | Trait Baru |
|-----------|-----------|
| `\App\Traits\ClearsAiMenuCache` | `\App\Shared\Traits\ClearsAiMenuCache` |

---

## 5. Langkah Eksekusi (Urutan Wajib)

### Step 1: Buat struktur folder baru
```bash
mkdir -p app/Central/Controllers app/Central/Models app/Central/Services
mkdir -p app/Tenant/Controllers/Web app/Tenant/Controllers/Api
mkdir -p app/Tenant/Models/Core app/Tenant/Models/Resto app/Tenant/Models/Ai
mkdir -p app/Tenant/Services
mkdir -p app/Shared/Traits app/Shared/Middleware app/Shared/Mail app/Shared/Jobs app/Shared/Listeners
```

### Step 2: Update `composer.json` PSR-4 autoload
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

### Step 3: Pindahkan file satu per satu
Untuk setiap file di tabel mapping (Section 3):
1. `mv` file ke lokasi baru
2. Update `namespace` di dalam file
3. Update semua `use` import di dalam file tersebut

### Step 4: Update semua file yang mereferensikan (Section 4)
Gunakan find-and-replace untuk setiap mapping namespace.

**Perhatian khusus untuk FQ inline references** (`\App\Models\...` tanpa `use`):
- Ada 22+ referensi fully-qualified di blade views dan controllers
- Ini paling mudah terlewat — cek manual setiap file di Section 4.6

### Step 5: Merge IpWhitelist middleware
Gabungkan `DuitkuIpWhitelist.php` dan `MidtransIpWhitelist.php` menjadi satu `IpWhitelist.php` di `app/Shared/Middleware/`.

### Step 6: Update internal references di model
Beberapa model mereferensikan model lain di dalam relasinya:
- `Product.php` → `Category::class`, `ProductVariant::class`, `ProductExtra::class`
- `Order.php` → `OrderItem::class`, `User::class`
- `ProductVariant.php` → `Product::class`, `VariantRecipe::class`, `AiPricingRule::class`
- dll.

Pastikan semua `use` di dalam model sudah benar setelah dipindah.

### Step 7: Jalankan composer dump-autoload
```bash
composer dump-autoload
```

### Step 8: Bersihkan cache
```bash
php artisan optimize:clear
rm -rf storage/framework/views/*
```

### Step 9: Verifikasi
```bash
# Cek tidak ada class not found
php artisan route:list

# Cek tidak ada error
php artisan config:clear
php artisan test
```

---

## 6. Checklist Verifikasi

- [ ] Semua folder baru terbuat
- [ ] `composer.json` autoload diupdate
- [ ] Semua file dipindahkan dan namespace diupdate
- [ ] Semua `use` import di ~78 file diupdate
- [ ] Semua FQ inline references (`\App\Models\...`) diupdate
- [ ] `composer dump-autoload` berhasil tanpa error
- [ ] `php artisan route:list` menampilkan semua route
- [ ] `php artisan optimize:clear` berhasil
- [ ] `storage/framework/views/` dihapus (compiled blade cache)
- [ ] `php artisan test` pass (jika ada test)
- [ ] Akses halaman utama di browser — tidak ada error 500

---

## 7. Rollback Plan

Jika ada error setelah refactor:
```bash
git checkout -- .
composer dump-autoload
php artisan optimize:clear
```

Pastikan semua perubahan di-commit per-step agar mudah di-revert sebagian jika perlu.

---

## 8. Catatan Penting

1. **Jangan pindahkan `Controller.php` (base class)** — tetap di `app/Http/Controllers/` karena di-extend oleh semua controller.

2. **`TenantUser` vs `User`** — `TenantUser` adalah model tenant (kasir/manager), `User` adalah model central (admin SaaS). Keduanya punya tabel `users` tapi di database berbeda.

3. **Trait `ClearsAiMenuCache`** — dipakai di dalam body class `Product`, `ProductVariant`, `ProductExtra`. Referensinya fully-qualified (`\App\Traits\ClearsAiMenuCache`), bukan via `use` statement. Harus diupdate manual.

4. **Observer `ProductObserver`** — tetap di `app/Observers/`, tapi update import `Product` dan `Quota` di dalamnya.

5. **Livewire components** — tidak dipindah. Namespace Livewire tetap `App\Livewire` dan views tetap di `resources/views/`.

6. **Console Commands** — tetap di `app/Console/Commands/`, tapi update import model yang direferensikan.

---

> **Selanjutnya:** Setelah Phase 2 selesai, lanjut ke [Phase 3: View & Asset Restructuring](.docs/project/architecture-plan.md#phase-3-view--asset-restructuring-23-jam).
