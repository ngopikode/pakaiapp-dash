# Fitur: Jam Operasional Toko (Operating Hours)

Dokumen ini mencatat rencana implementasi, keputusan arsitektur, dan konteks untuk fitur Jam Operasional Toko.

## Konteks & Masalah Saat Ini
1. **Tidak ada jadwal jam operasional** - Store hanya punya toggle `is_active` (buka/tutup manual).
2. **Tidak ada proteksi API** - Order API `POST /api/orders` tidak mengecek apakah toko sedang buka atau tutup; customer bisa checkout asalkan URL menu diketahui.
3. **MenuController tanpa gate** - Halaman detail produk (`/menu/slug`) dapat diakses tanpa melewati gate `layouts/store.blade.php`, sehingga menu bisa dilihat (dan diorder) walau toko diset "store-not-found" (tutup manual).
4. **Performa kueri setting** - `StoreSetting::first()` dipanggil tanpa cache di setiap page load, API call, dan webhook, menghasilkan beban database yang tinggi di tabel per-tenant.

## Keputusan Arsitektur

### 1. Struktur Data (Migration & Model)
- Tambah kolom `operating_hours` (JSON) dan `use_same_hours` (BOOLEAN) ke tabel `store_settings`.
- Format JSON menyimpan jadwal per hari (senin-minggu) dan jadwal `default`.
- Method `isOpenNow()` dan `getTodayHours()` di model `StoreSetting` menggunakan `Carbon::now('Asia/Jakarta')` untuk mengecek status saat ini (mendukung pergantian hari/overnight hours).

### 2. Strategi Caching
- Menggunakan driver cache default (database) dengan fallback/bisa diganti ke Redis.
- Cache level: **Forever + Bust on Save**.
- Implementasi via trait `ClearsStoreSettingCache` (mirip pola `ClearsAiMenuCache` yang ada) yang akan melupakan cache key `'store_setting_' . tenant('id')` pada event `saved` dan `deleted`.
- Akses via static method `StoreSetting::cached()`.

### 3. Middleware & Keamanan API
- Membuat middleware baru `CheckStoreOpen` di `app/Shared/Middleware/`.
- Hanya diterapkan di route `POST /api/orders` (pembuatan transaksi). Route `GET /api/restaurant` dan API publik lainnya tetap bebas diakses.
- Jika toko tutup, kembalikan HTTP 422 JSON payload `{ message: "Toko sedang tutup." }`.
- Kasir/dashboard *tidak* terpengaruh middleware ini (diperbolehkan order internal kapan saja).

### 4. UI/UX Storefront Resto
- **Hero Badge:** Teks badge "Buka Sekarang/Tutup" otomatis menyesuaikan data jam operasional hari ini. Teks statis `hero_status_text` menjadi fallback jika merchant tidak mengatur jam.
- **Closed Banner:** Menampilkan banner peringatan (overlay/sticky) di atas menu jika toko sedang di luar jam operasional.
- **Block Order:** Tombol "Tambah ke Keranjang" pada produk dan tombol "Checkout" di dalam modal akan didisable (diberi kondisi berdasarkan status `storeClosed` di Alpine).
- **Detail Produk:** `MenuController` akan turut mengecek status buka/tutup dan memberikan variable status ke view, mencegah celah back-door link.

## Fase Implementasi

**Fase 1: Persiapan Arsitektur (Selesai)**
- ✅ Refactor: Pindah model `ProductExtra` dari `Models/Core/` ke `Models/Resto/` (menghindari error saat cross-tenant logic).
- ✅ Update impor yang berkaitan di seluruh codebase.
- ✅ Siapkan migration directories untuk tenant `retail` dan `service`.
- ✅ Migration kolom `operating_hours` ke `store_settings`.
- ✅ Method `isOpenNow()` dan `getTodayHours()` pada model.
- ✅ Expose data jam buka di `RestaurantApiController`.

**Fase 2: Middleware & Caching (Target Berikutnya)**
- [ ] Buat trait `ClearsStoreSettingCache`.
- [ ] Implementasikan `StoreSetting::cached()`.
- [ ] Buat dan daftarkan middleware `CheckStoreOpen`.
- [ ] Terapkan middleware ke `routes/tenant/api.php`.
- [ ] Update semua pemanggilan `StoreSetting::first()` menjadi `StoreSetting::cached()`.

**Fase 3: Implementasi UI Storefront (Target Akhir)**
- [ ] Update `layouts/store.blade.php` untuk passing `$isOpenNow` ke seluruh komponen.
- [ ] Modifikasi partial `_hero.blade.php` agar dinamis.
- [ ] Tambahkan partial `_closed-banner.blade.php` dan panggil di layout.
- [ ] Disable tombol pada `product-grid.blade.php` dan `checkout-modal.blade.php` berdasarkan state buka/tutup.
- [ ] Pasang gate pengecekan di `MenuController`.

---
*Dokumen ini dibuat dan diperbarui secara berkala selama implementasi.*