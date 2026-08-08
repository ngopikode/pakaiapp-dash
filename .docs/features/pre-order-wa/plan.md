# Fitur: Pre-Order & Pengiriman Terjadwal (WA Checkout)

> Referensi: [Project Map](../../project-map.md) · [Architecture Decisions](../../decisions/)

Fitur ini menambahkan opsi **checkout via WhatsApp** dan **pengiriman terjadwal (Pre-Order)** ke sistem multi-tenant Pakaiapp, yang bisa diaktifkan secara terpisah, tanpa merusak alur POS/resto yang sudah berjalan.

**Target pertama:** tenant tipe `retail` (pedagang sayur/bumbu seperti Nuala Sayur).

---

## Konteks & Masalah

Sistem existing sudah mendukung order online (`is_online = true`) dengan dua payment gateway (Duitku & Midtrans). Namun
belum ada mode untuk tenant yang:

1. **Tidak butuh payment gateway** — pembayaran via QRIS statis (foto QR toko) atau COD (Checkout WhatsApp).
2. **Butuh jadwal pengiriman** — pembeli memilih tanggal + slot jam kirim.
3. **Butuh rekap agregasi** — dashboard merangkum total bahan per produk untuk belanja pasar.

---

## Keputusan Arsitektur: Modular Flags

Kita memisahkan fitur menjadi dua boolean flag independen di tabel `store_settings`:

1. **`is_wa_checkout_active`**: Mode Checkout WhatsApp
   - Mengalihkan halaman checkout web menjadi manual pesan via WA (tidak pakai PG).
   - Menyembunyikan otomatis fitur Kasir dan KDS dari Sidebar & Setting (karena pesanan dilayani via WA).
   
2. **`is_preorder_active`**: Mode Pengiriman Terjadwal
   - Mewajibkan pembeli memilih **Tanggal** & **Slot Pengiriman**.
   - Menambahkan menu **Pesanan Terjadwal** dan **Rekap Belanja Pasar** ke Sidebar.
   - Bisa dipakai bersama WA Checkout, atau dengan metode POS normal (Midtrans/Duitku).

---

### 2. Endpoint Baru `/api/preorders` — BUKAN override `/api/orders`

Endpoint existing `/api/orders` beserta `OrderService::processOrder()` **tidak disentuh sama sekali**. Pre-order punya
controller + service sendiri. Ini menghilangkan risiko regresi ke tenant POS/resto.

---

### 3. Kolom delivery ditambah ke tabel `orders` yang ada (nullable)

Nullable columns — tidak memengaruhi order POS lama:

| Kolom              | Type                       | Keterangan                              |
|--------------------|----------------------------|-----------------------------------------|
| `delivery_date`    | `date` nullable            | Tanggal pengiriman yang dipilih pembeli |
| `delivery_slot_id` | `bigint unsigned` nullable | FK ke `delivery_slots.id`               |
| `delivery_zone_id` | `bigint unsigned` nullable | FK ke `delivery_zones.id`               |
| `shipping_cost`    | `decimal(10,2)` default 0  | Ongkir yang dibebankan (0 = gratis)     |
| `customer_address` | `text` nullable            | Alamat pengiriman lengkap               |

---

### 4. Kolom baru di `store_settings`

| Kolom           | Type                      | Default | Keterangan                    |
|-----------------|---------------------------|---------|-------------------------------|
| `is_wa_checkout_active` | `enum('pos','WA Checkout')` | `'pos'` | Feature flag utama            |
| `cutoff_time`   | `time`                    | `null`  | Jam batas pesan (ex: `04:00`) |
| `qris_image`    | `varchar(255)`            | `null`  | Path gambar QRIS statis       |

> `whatsapp_number` yang sudah ada di `store_settings` dipakai untuk redirect WA admin — tidak perlu kolom baru.

---

### 5. Tabel Baru

**`delivery_slots`** — konfigurasi slot pengiriman per tenant:

| Kolom                      | Type                                    |
|----------------------------|-----------------------------------------|
| `id`                       | bigint PK                               |
| `name`                     | varchar(100) — ex: "Pagi (06:00–09:00)" |
| `start_time`               | time                                    |
| `end_time`                 | time                                    |
| `max_orders`               | int — kuota per slot per hari           |
| `is_active`                | boolean                                 |
| `created_at`, `updated_at` | timestamps                              |

**`delivery_zones`** — konfigurasi zona ongkir per tenant:

| Kolom                      | Type                                        |
|----------------------------|---------------------------------------------|
| `id`                       | bigint PK                                   |
| `name`                     | varchar(100) — ex: "Sri Gunting"            |
| `shipping_cost`            | decimal(10,2)                               |
| `min_free_shipping`        | decimal(10,2) — 0 = tidak ada gratis ongkir |
| `is_active`                | boolean                                     |
| `created_at`, `updated_at` | timestamps                                  |

---

### 6. Cut-Off Time Logic

Di dalam `PreOrderService::resolveEarliestDeliveryDate()`:

```
jam sekarang (WIB) < cutoff_time → tanggal pengiriman tercepat = HARI INI
jam sekarang (WIB) >= cutoff_time → tanggal pengiriman tercepat = BESOK
cutoff_time null → selalu BESOK (aman default)
```

Timezone hardcoded `Asia/Jakarta` — konsisten dengan `StoreSetting::isOpenNow()` yang sudah ada.

---

### 7. Slot Quota Check

- `GET /api/preorders/slots?date=` → returns slot + `available` (max_orders - sudah dipesan hari itu).
- Saat `POST /api/preorders`: query
  `COUNT orders WHERE delivery_date = ? AND delivery_slot_id = ? AND status != 'cancelled'` vs `max_orders`. Jika
  penuh → 422.

---

### 8. Market Aggregator — Query Murni, Tanpa Tabel Baru

```sql
SELECT oi.product_name, oi.variant_name, SUM(oi.quantity) AS total_qty
FROM order_items oi
         JOIN orders o ON o.id = oi.order_id
WHERE o.delivery_date = :date
  AND o.status != 'cancelled'
GROUP BY oi.product_name, oi.variant_name
ORDER BY oi.product_name
```

`order_items` yang sudah ada memiliki semua data yang dibutuhkan. Output disalin sebagai teks WA (PDF dibuang — YAGNI).

---

### 9. Bulk Complete

`PreOrderService::completeAllForDate(Carbon $date)` — update batch semua pesanan `pending` di tanggal tersebut menjadi
`paid`. Tidak memanggil `processRevenue()` ke Wallet (ponytail: tambahkan di iterasi berikutnya jika analytics
dibutuhkan).

---

## File yang Dibuat/Dimodifikasi

### Migrasi Tenant (4 file baru di `database/migrations/tenant/core/`)

1. `2026_08_xx_add_WA Checkout_fields_to_store_settings.php`
2. `2026_08_xx_create_delivery_slots_table.php`
3. `2026_08_xx_create_delivery_zones_table.php`
4. `2026_08_xx_add_delivery_fields_to_orders.php`

### Model Baru (Tenant)

- `app/Tenant/Models/Core/DeliverySlot.php` + Factory
- `app/Tenant/Models/Core/DeliveryZone.php` + Factory

### Model Dimodifikasi

| File               | Perubahan                                                                                                     |
|--------------------|---------------------------------------------------------------------------------------------------------------|
| `StoreSetting.php` | Fillable baru, `isDirectWaMode(): bool`, casts `is_wa_checkout_active`                                                |
| `Order.php`        | Fillable baru (`delivery_*`, `shipping_cost`, `customer_address`), relasi `deliverySlot()` + `deliveryZone()` |

### DTO Baru

- `app/Tenant/Data/CreatePreOrderData.php`

### Service Baru

- `app/Tenant/Services/PreOrderService.php`
    - `createPreOrder(CreatePreOrderData $data, array $items): Order`
    - `resolveEarliestDeliveryDate(StoreSetting $setting): Carbon`
    - `getSlotAvailability(Carbon $date): Collection`
    - `getMarketRecap(Carbon $date): Collection`
    - `completeAllForDate(Carbon $date): int` (returns jumlah order yang diselesaikan)
    - `buildWaMessage(Order $order): string`

### Controller Baru

- `app/Tenant/Controllers/Api/PreOrderApiController.php`
    - `GET /api/preorders/config` — earliest delivery date + timezone config
    - `GET /api/preorders/slots?date=` — slot list + kuota tersisa
    - `POST /api/preorders` — buat pre-order (throttle: orders)

### Route Tambahan

`routes/tenant/api.php`:

```php
Route::prefix('preorders')->group(function () {
    Route::get('config', [PreOrderApiController::class, 'config']);
    Route::get('slots', [PreOrderApiController::class, 'slots']);
    Route::post('/', [PreOrderApiController::class, 'store'])->middleware('throttle:orders');
});
```

`routes/tenant.php`:

```php
Route::get('/pre-order', /* daily-orders */)->middleware(['auth', 'role:manager,cashier']);
Route::get('/pre-order/recap', /* market-recap */)->middleware(['auth', 'role:manager']);
```

### Dashboard Merchant (Livewire)

- `resources/views/pages/tenant/pre-order/⚡daily-orders.blade.php` — daftar pesanan terjadwal + tombol "Selesaikan
  Semua"
- `resources/views/pages/tenant/pre-order/⚡market-recap.blade.php` — rekap belanja + salin teks WA

### Setting UI

Tambah section **"Mode Pre-Order"** di halaman `/store-setting` existing. Update `StoreSettingFormData` dan
`SettingService::saveFromForm()`.

---

## Yang TIDAK Diubah

| Hal | Alasan |
|---|---|
| `POST /api/orders` | Tetap untuk POS. Tidak disentuh. |
| `OrderService::processOrder()` | Pre-order punya service sendiri. |
| Payment Gateway (Duitku/Midtrans) | Tidak dipanggil dari alur `WA Checkout`. |
| `KitchenUpdated` event | Tidak relevan untuk mode pre-order. |
| Wallet balance / revenue tracking | Disederhanakan untuk v1. |
| `categories`, `products`, `product_variants` | Dipakai apa adanya — katalog sama. |
| Sistem jam operasional (`operating_hours`) | Tetap satu range per hari, untuk gate "kapan toko bisa dipesan". Tidak bentrok dengan `delivery_slots`. |
| Route guard & middleware | Akses dikontrol `role:manager` seperti biasa. Sidebar yang menyembunyikan menu, bukan route. |

---

## Dampak Otomatis Mode `WA Checkout`

Semua perubahan ini terjadi **otomatis** saat merchant mengaktifkan mode `WA Checkout` di setting. Tidak ada aksi tambahan.

| Fitur | Mode `pos` (default) | Mode `WA Checkout` |
|---|---|---|
| Menu "Kasir / POS" di sidebar | ✅ Tampil | ❌ Disembunyikan |
| Menu "Layar Dapur" di sidebar | ✅ Tampil jika `is_kitchen_active` | ❌ Disembunyikan |
| Toggle "Sesi Shift Kasir" di setting | ✅ Tampil | ❌ Disembunyikan (`x-show`) |
| Toggle "Layar Dapur (KDS)" di setting | ✅ Tampil jika resto | ❌ Disembunyikan (`x-show`) |
| Menu "Pesanan Terjadwal" di sidebar | ❌ Tidak ada | ✅ Muncul |
| Menu "Rekap Belanja Pasar" di sidebar | ❌ Tidak ada | ✅ Muncul |
| Produk, Katalog, Wallet, Riwayat | ✅ | ✅ Sama |

### Catatan: Jam Operasional vs Slot Pengiriman

| | `operating_hours` (existing) | `delivery_slots` (pre-order) |
|---|---|---|
| **Fungsi** | Gate kapan toko bisa menerima order | Jam keberangkatan kurir |
| **Bisa >1x sehari?** | Tidak (satu range per hari) | Ya, bisa banyak slot |
| **Siapa yang pakai** | Semua tenant | Hanya `WA Checkout` |

---

## Rencana Implementasi (4 Fase)

| Fase       | Cakupan                                                                              | Model             | Status     |
|------------|--------------------------------------------------------------------------------------|-------------------|------------|
| **Fase 1** | Migrasi + Model (DeliverySlot, DeliveryZone, update StoreSetting & Order)            | Model murah       | ✅ Shipped |
| **Fase 2** | DTO + PreOrderService + PreOrderApiController + routes + tests                       | **Claude Sonnet** | ✅ Shipped |
| **Fase 3** | Livewire dashboard merchant (daily orders + market recap) + setting UI section       | Model murah       | ✅ Shipped |
| **Fase 3.1** | Patch sidebar (hide Kasir/KDS di WA Checkout, tambah menu pre-order) + patch setting blade (x-show Shift & KDS) | **Claude Sonnet** | ✅ Shipped |
| **Fase 4** | Frontend katalog customer (checkout 3-step Livewire+Alpine, detect `WA Checkout` mode) | Model murah       | ⏳ Planned |

---

## Session Checklist — Wajib Dibaca di Awal Setiap Session

> **Tujuan:** Agar model pengerjanya tidak perlu eksplorasi — langsung tahu file mana yang harus dibaca dan urutan
> kerjanya. Hemat token, menghindari halusinasi.

---

### Session A — Fase 1: Migrasi + Model

**Model:** murah (haiku / flash)

**Baca dulu sebelum mulai:**

1. `.docs/features/pre-order-wa/plan.md` — dokumen ini (konteks penuh)
2. `.docs/project-map.md` — pahami struktur direktori
3. `.docs/references/laravel13/PATTERNS.md` — aturan penulisan PHP
4. `app/Tenant/Models/Core/StoreSetting.php` — pola Fillable + casts + method existing
5. `app/Tenant/Models/Core/Order.php` — pola Fillable + relasi existing
6. `database/migrations/tenant/core/2026_07_27_000001_add_operating_hours_to_store_settings.php` — contoh migrasi
   `alter table` di proyek ini
7. `database/migrations/tenant/core/2026_04_24_141029_create_orders_table.php` — referensi struktur tabel orders

**Yang dikerjakan (urutan):**

1. Buat `database/migrations/tenant/core/2026_08_xx_add_WA Checkout_fields_to_store_settings.php`
    - Tambah: `is_wa_checkout_active` enum ('pos','WA Checkout') default 'pos', `cutoff_time` time nullable, `qris_image` varchar
      (255) nullable
2. Buat `database/migrations/tenant/core/2026_08_xx_create_delivery_slots_table.php`
3. Buat `database/migrations/tenant/core/2026_08_xx_create_delivery_zones_table.php`
4. Buat `database/migrations/tenant/core/2026_08_xx_add_delivery_fields_to_orders.php`
    - Tambah: `delivery_date` date nullable, `delivery_slot_id` unsignedBigInt nullable, `delivery_zone_id`
      unsignedBigInt nullable, `shipping_cost` decimal (10,2) default 0, `customer_address` text nullable
5. Buat `app/Tenant/Models/Core/DeliverySlot.php` (Fillable, casts, relasi ke Order)
6. Buat `app/Tenant/Models/Core/DeliveryZone.php` (Fillable, casts, relasi ke Order)
7. Buat `database/factories/DeliverySlotFactory.php`
8. Buat `database/factories/DeliveryZoneFactory.php`
9. Update `app/Tenant/Models/Core/StoreSetting.php`:
    - Tambah fillable: `is_wa_checkout_active`, `cutoff_time`, `qris_image`
    - Tambah cast: `is_wa_checkout_active` (string), `cutoff_time` (string)
    - Tambah method: `public function isDirectWaMode(): bool { return $this->is_wa_checkout_active === 'WA Checkout'; }`
    - Tambah konstanta: `const MODE_POS = 'pos'` dan `const MODE_DIRECT_WA = 'WA Checkout'`
10. Update `app/Tenant/Models/Core/Order.php`:
    - Tambah fillable: `delivery_date`, `delivery_slot_id`, `delivery_zone_id`, `shipping_cost`, `customer_address`
    - Tambah cast: `delivery_date` → `'date'`
    - Tambah relasi: `deliverySlot(): BelongsTo` dan `deliveryZone(): BelongsTo`
11. Jalankan `vendor/bin/pint --dirty --format agent`
12. **Verifikasi:** `php artisan tenants:run migrate --no-interaction` pastikan tidak error

**Jangan dikerjakan di session ini:** DTO, Service, Controller, Livewire, routes.

---

### Session B — Fase 2: Backend Logic (KRITIKAL)

**Model:** Claude Sonnet

**Baca dulu sebelum mulai:**

1. `.docs/features/pre-order-wa/plan.md` — dokumen ini
2. `.docs/project-map.md`
3. `.docs/references/laravel13/PATTERNS.md` — wajib, terutama bagian DB Transaction, DTO, Thin Controller
4. `app/Tenant/Models/Core/StoreSetting.php` — pastikan Fase 1 sudah selesai, cek `isDirectWaMode()` ada
5. `app/Tenant/Models/Core/Order.php` — cek relasi delivery sudah ada
6. `app/Tenant/Models/Core/DeliverySlot.php` — hasil Fase 1
7. `app/Tenant/Models/Core/DeliveryZone.php` — hasil Fase 1
8. `app/Tenant/Services/OrderService.php` — **HANYA BACA, jangan diubah.** Pahami pola service yang sudah ada: lazy
   getter, DB::beginTransaction, processOrder signature.
9. `app/Tenant/Data/ProcessOrderData.php` — pola DTO yang sudah ada
10. `app/Tenant/Controllers/Api/OrderApiController.php` — pola controller API yang sudah ada
11. `routes/tenant/api.php` — lihat struktur route existing
12. `app/Shared/Traits/ApiResponserTrait.php` — pahami `successResponse`, `failResponse`, `errorResponse`

**Yang dikerjakan (urutan):**

1. Buat `app/Tenant/Data/CreatePreOrderData.php`
    - Fields: `customerName` (string), `customerPhone` (?string), `customerAddress` (string), `deliveryDate` (string —
      format Y-m-d), `deliverySlotId` (int), `deliveryZoneId` (int), `paymentMethod` (string — 'qris'|'cash'), `notes`
      (?string default null)
2. Buat `app/Tenant/Services/PreOrderService.php` dengan semua method (lihat detail di bawah)
3. Buat `app/Tenant/Controllers/Api/PreOrderApiController.php`
4. Daftarkan routes di `routes/tenant/api.php`
5. Buat feature test `tests/Feature/PreOrderApiTest.php`
6. Jalankan `php artisan test --compact --filter=PreOrder`
7. Jalankan `vendor/bin/pint --dirty --format agent`

**Detail PreOrderService — logika per method:**

```
createPreOrder(CreatePreOrderData $data, array $items): Order
  - Validasi: delivery_date >= resolveEarliestDeliveryDate()
  - Validasi: slot quota (COUNT orders untuk slot+tanggal vs max_orders) — throw Exception jika penuh
  - Hitung shipping_cost: zone->shipping_cost, set ke 0 jika subtotal >= zone->min_free_shipping (dan min_free_shipping > 0)
  - Hitung subtotal dari items (Product::whereIn + ProductVariant::whereIn, lockForUpdate)
  - total_price = subtotal + shipping_cost (TIDAK ada tax/service charge di mode DIRECT_WA)
  - Buat Order: status='pending', order_type='online', is_online=true, payment_method=$data->paymentMethod
  - Insert OrderItems (bulk insert)
  - TIDAK decrement stock (pre-order belum dikonfirmasi pasti)
  - TIDAK fire KitchenUpdated
  - TIDAK panggil Wallet/revenue
  - Return Order
  - Wrapped DB::beginTransaction / commit / rollBack

resolveEarliestDeliveryDate(StoreSetting $setting): Carbon
  - now = Carbon::now('Asia/Jakarta')
  - if cutoff_time null → return today()->addDay()
  - cutoff = Carbon::createFromFormat('H:i', $setting->cutoff_time, 'Asia/Jakarta')
  - if now < cutoff → return today()
  - else → return today()->addDay()

getSlotAvailability(Carbon $date): Collection
  - DeliverySlot::where('is_active', true)->get()
  - Untuk setiap slot: COUNT orders WHERE delivery_date=$date AND delivery_slot_id=slot->id AND status!='cancelled'
  - Return collection of: id, name, start_time, end_time, max_orders, booked, available (max-booked), is_full

getMarketRecap(Carbon $date): Collection
  - Query agregasi order_items JOIN orders (lihat SQL di atas di bagian Keputusan Arsitektur)
  - Return collection of: product_name, variant_name, total_qty

completeAllForDate(Carbon $date): int
  - DB::beginTransaction
  - Order::where('delivery_date', $date)->where('status', 'pending')->update(['status' => 'paid'])
  - Return jumlah rows updated
  - DB::commit / rollBack

buildWaMessage(Order $order): string
  - Load relasi: order->load('items', 'deliverySlot', 'deliveryZone')
  - Format teks WA yang rapi:
    "🛒 *Pesanan Baru - {invoice_code}*
    Nama: {customer_name}
    Alamat: {customer_address}
    Tgl Kirim: {delivery_date formatted}
    Slot: {slot name}
    Zona: {zone name}
    ...items list...
    Subtotal: Rp ...
    Ongkir: Rp ...
    *Total: Rp ...*
    Pembayaran: {QRIS/COD}"
  - Return urlencode-ready string (tapi JANGAN urlencode di sini — itu tugas frontend/controller)
```

**Detail PreOrderApiController:**

```
config(): JsonResponse
  - Load StoreSetting::cached()
  - Return: earliest_delivery_date (Y-m-d), cutoff_time, zones (DeliveryZone::where('is_active',true)->get())

slots(Request $request): JsonResponse
  - Validate: date (required, date, after_or_equal:today)
  - Return: $this->preOrderService()->getSlotAvailability(Carbon::parse($request->date))

store(Request $request): JsonResponse
  - Validate semua field CreatePreOrderData + items[]
  - Guard: StoreSetting::cached()->isDirectWaMode() || return failResponse 403
  - Buat DTO, panggil createPreOrder()
  - Return: order_id, invoice_code, wa_url (format: "https://wa.me/{whatsapp_number}?text={urlencode(buildWaMessage($order))}")
```

**Jangan dikerjakan di session ini:** Livewire, view, setting UI, frontend katalog.

---

### Session C — Fase 3: Dashboard Merchant

**Model:** murah (haiku / flash)

**Baca dulu sebelum mulai:**

1. `.docs/features/pre-order-wa/plan.md`
2. `.docs/references/livewire4/PATTERNS.md`
3. `resources/views/pages/tenant/order/` — lihat pola tampilan daftar order existing sebagai referensi UI
4. `resources/views/pages/tenant/setting/` — lihat pola halaman setting existing
5. `app/Tenant/Data/StoreSettingFormData.php` — untuk menambah field baru ke form setting
6. `app/Tenant/Services/SettingService.php` — khususnya `saveFromForm()`
7. `app/Tenant/Services/PreOrderService.php` — hasil Fase 2, untuk tahu method yang tersedia
8. `routes/tenant.php` — untuk menambah route baru

**Yang dikerjakan (urutan):**

1. Update `StoreSettingFormData.php` — tambah `checkoutMode`, `cutoffTime`, `qrisImage`
2. Update `SettingService::saveFromForm()` — simpan 3 field baru + handle upload `qris_image`
3. Tambah section "Mode Pre-Order" di Livewire page `/store-setting` existing
4. Buat Livewire page `pages.tenant.pre-order.daily-orders`
5. Buat Livewire page `pages.tenant.pre-order.market-recap`
6. Tambah route di `routes/tenant.php`
7. Jalankan `vendor/bin/pint --dirty --format agent`

---

### Session D — Fase 4: Frontend Katalog Customer

**Model:** murah (haiku / flash)

**Baca dulu sebelum mulai:**

1. `.docs/features/pre-order-wa/plan.md`
2. `resources/views/layouts/store.blade.php` — perhatikan `x-data="storeApp"` dan `data-*` attributes
3. `resources/js/store.js` — pelajari bagaimana Alpine state untuk checkout bekerja (misal: `checkoutStep`, `submitOrder()`)
4. `resources/views/pages/tenant/store/resto/modals/checkout-modal.blade.php` — form checkout 100% Client-Side.
5. `app/Tenant/Controllers/Api/PreOrderApiController.php` — endpoint pre-order `/api/preorders/`

**Yang dikerjakan:**

1. **Update `store.blade.php` layout**:
   - Inject `data-wa-checkout-active` dan `data-preorder-active` dari setting toko.
   - Inject config pre-order dari `PreOrderApiController::config()` ke dalam HTML (atau via Axios di `init()` store.js).
2. **Update `store.js`**:
   - Tangkap flag WA Checkout dan Pre-Order.
   - Modifikasi `submitOrder()`: Jika `is_preorder_active`, validasi slot dan tgl kirim lalu post ke `/api/preorders` bukan `/api/orders`.
   - Modifikasi `submitOrder()`: Jika `is_wa_checkout_active`, skip Midtrans/Duitku, langsung proses dan buka tab `wa_url`.
3. **Update `checkout-modal.blade.php`**:
   - Tambahkan Step baru atau input field tambahan (Pilih Tanggal, Pilih Slot) jika `is_preorder_active` = 1.
   - Sembunyikan opsi metode pembayaran gateway jika `is_wa_checkout_active` = 1.
4. Jalankan `npm run build` (karena `store.js` di-bundle oleh Vite).

---

## Tech Debt (Ponytail Notes)

- **Pre-Order per Produk:** Saat ini Pre-Order berlaku global (seluruh isi keranjang). Ke depannya perlu dikembangkan agar Pre-Order bisa di-set spesifik per produk (barang A PO, barang B ready stock), lengkap dengan logika split pengiriman di keranjang.
- `createPreOrder()` tidak decrement stock — tambahkan jika merchant butuh tracking stok reserved.
- Notifikasi realtime saat pesanan masuk tidak ada di v1 — merchant refresh dashboard manual.
- PDF export rekap pasar tidak ada — salin teks WA sebagai pengganti.
