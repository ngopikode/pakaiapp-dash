# Fitur: Jam Operasional Toko (Operating Hours)

> Referensi: [Project Map](../../project-map.md) · [Architecture Decisions](../../decisions/) · [API Spec](../../project/api-spec.md)

Dokumen ini mencatat rencana implementasi, keputusan arsitektur, dan konteks untuk fitur Jam Operasional Toko.

---

## Konteks & Masalah Saat Ini

1. **Tidak ada jadwal jam operasional** — Store hanya punya toggle `is_active` (buka/tutup manual).
2. **Tidak ada proteksi API** — Order API `POST /api/orders` tidak mengecek apakah toko sedang buka atau tutup; customer bisa checkout asalkan URL menu diketahui.
3. **MenuController tanpa gate** — Halaman detail produk (`/menu/slug`) dapat diakses tanpa melewati gate `layouts/store.blade.php`, sehingga menu bisa dilihat (dan diorder) walau toko seharusnya tutup.
4. **Performa kueri setting** — `StoreSetting::first()` dipanggil tanpa cache di setiap page load, API call, dan order request, menghasilkan beban DB yang tidak perlu.

---

## Keputusan Arsitektur

### 1. Struktur Data (Migration & Model)

- Tambah kolom `operating_hours` (JSON) dan `use_same_hours` (BOOLEAN) ke tabel `store_settings`.
- Format JSON `operating_hours`:
  ```json
  {
    "default":   { "open": "08:00", "close": "22:00", "is_closed": false },
    "monday":    { "open": "08:00", "close": "22:00", "is_closed": false },
    "tuesday":   { "open": "08:00", "close": "22:00", "is_closed": false },
    "wednesday": { "open": "08:00", "close": "22:00", "is_closed": false },
    "thursday":  { "open": "08:00", "close": "22:00", "is_closed": false },
    "friday":    { "open": "08:00", "close": "22:00", "is_closed": false },
    "saturday":  { "open": "09:00", "close": "20:00", "is_closed": false },
    "sunday":    { "open": "10:00", "close": "17:00", "is_closed": true }
  }
  ```
- `use_same_hours = true` → hanya key `default` yang dipakai untuk semua hari.
- Method `isOpenNow()` dan `getTodayHours()` di `StoreSetting` menggunakan `Carbon::now('Asia/Jakarta')` — timezone hardcoded, mendukung overnight hours.

**File terdampak:**
- `database/migrations/tenant/core/2026_07_27_000001_add_operating_hours_to_store_settings.php`
- `app/Tenant/Models/Core/StoreSetting.php`

### 2. Strategi Caching

- Driver: default codebase (`database`), bisa diganti Redis tanpa ubah kode.
- Cache level: **Forever + Bust on Save** — mirip pola `ClearsAiMenuCache` yang sudah ada.
- Cache key: `'store_setting_' . tenant('id')` — per tenant, tidak clash.
- Akses via `StoreSetting::cached()` — semua caller wajib pakai ini, bukan `::first()`.

**File terdampak (Fase 2):**
- `app/Shared/Traits/ClearsStoreSettingCache.php` ← CREATE
- `app/Tenant/Models/Core/StoreSetting.php` ← tambah trait + static `cached()`
- `app/Tenant/Controllers/Api/RestaurantApiController.php` ← ganti `::first()` → `::cached()`
- `resources/views/layouts/store.blade.php` ← ganti `::first()` → `::cached()`

### 3. Middleware & Keamanan API

- Middleware baru `CheckStoreOpen` di `app/Shared/Middleware/`, alias `store.open`.
- Hanya dipasang di `POST /api/orders`. Route lain tetap bebas.
- Logika: jika `operating_hours` null → skip (toko dianggap selalu buka). Jika `isOpenNow() = false` → return HTTP 422:
  ```json
  { "message": "Toko sedang tutup.", "today_hours": { "open": "08:00", "close": "22:00", "is_closed": false } }
  ```
- Kasir/dashboard tidak terpengaruh (middleware hanya di API publik).

**File terdampak (Fase 2):**
- `app/Shared/Middleware/CheckStoreOpen.php` ← CREATE
- `bootstrap/app.php` ← register alias `store.open`
- `routes/tenant/api.php` ← pasang middleware di `POST /api/orders`

### 4. UI/UX Storefront Resto

- **Hero Badge:** Otomatis dari `$isOpenNow` + `$todayHours`. Fallback ke `hero_status_text` jika `operating_hours` null.
- **Closed Banner:** Sticky banner di atas menu saat toko tutup. Menu tetap visible, tapi order diblokir.
- **Block Order FE:** `storeClosed` diinjeksi ke Alpine `x-data`. Tombol "Tambah" dan "Checkout" disabled saat `storeClosed = true`.
- **MenuController gate:** Pass `$isOpenNow` ke view detail produk, mencegah celah back-door.

**File terdampak (Fase 3):**
- `resources/views/layouts/store.blade.php`
- `resources/views/pages/tenant/store/resto/partials/_hero.blade.php`
- `resources/views/layouts/_partials/_closed-banner.blade.php` ← CREATE
- `resources/views/pages/tenant/store/resto/⚡product-grid/product-grid.blade.php`
- `resources/views/pages/tenant/store/resto/modals/checkout-modal.blade.php`
- `app/Tenant/Controllers/Web/MenuController.php`

---

## Fase Implementasi

### Fase 1: Persiapan Arsitektur ✅ Selesai (2026-07-27)

- ✅ Refactor: Pindah `ProductExtra` dari `Models/Core/` → `Models/Resto/`
- ✅ Update semua import `ProductExtra` di `OrderService`, `Product`, `ai-floating-chat`
- ✅ Siapkan direktori migration `tenant/retail/` dan `tenant/service/`
- ✅ Migration `operating_hours` + `use_same_hours` ke `store_settings`
- ✅ Method `isOpenNow()` dan `getTodayHours()` pada `StoreSetting`
- ✅ Expose data jam buka di `GET /api/restaurant`

### Fase 2: Middleware & Caching 🔄 Berikutnya

- [ ] Buat `app/Shared/Traits/ClearsStoreSettingCache.php`
- [ ] Tambahkan trait + `static cached()` ke `StoreSetting`
- [ ] Buat `app/Shared/Middleware/CheckStoreOpen.php`
- [ ] Register alias `store.open` di `bootstrap/app.php`
- [ ] Pasang middleware ke `POST /api/orders` di `routes/tenant/api.php`
- [ ] Ganti semua `StoreSetting::first()` → `StoreSetting::cached()`

### Fase 3: UI Storefront Resto 📋 Planned

- [ ] Update `layouts/store.blade.php` — inject `$isOpenNow`, `$todayHours`
- [ ] Modifikasi `_hero.blade.php` — badge dinamis dari `$isOpenNow` + `$todayHours`
- [ ] Buat `_closed-banner.blade.php` — banner sticky saat tutup
- [ ] `product-grid.blade.php` — disable tombol "Tambah" saat `storeClosed`
- [ ] `checkout-modal.blade.php` — disable checkout saat `storeClosed`
- [ ] `MenuController` — gate `isOpenNow()`, pass state ke view

---

*Dokumen ini diperbarui seiring perkembangan implementasi.*
