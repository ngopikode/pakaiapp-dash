# Fitur: Modul Inventory & Sesi Kasir (Z-Report) — Full Master Plan

> Referensi: [Project Map](../../project-map.md) · [Laravel 13 Patterns](../../references/laravel13/PATTERNS.md) · [Livewire 4 Patterns](../../references/livewire4/PATTERNS.md) · [Phase 3 UI Flow](./phase-3-ui-flow.md)

Dokumen ini adalah *master plan* lengkap dari seluruh fase implementasi Modul Inventory & Sesi Kasir, mencakup database, backend, dan frontend secara berurutan.

---

## Status Implementasi

| Phase | Deskripsi | Status |
|-------|-----------|--------|
| Phase 1 | Database Migration & Model | ✅ Selesai |
| Phase 2A | OrderService Refactor (Shift Ledger + Extra Auto-Deduct) | ✅ Selesai |
| Phase 2B | BillingService & TenantWalletService (Multi-Wallet) | ✅ Selesai |
| Phase 2C | ShiftService (Business Logic Buka/Tutup Shift) | 🔲 Belum |
| Phase 2D | DTO untuk Shift | 🔲 Belum |
| Phase 3A | Toggle `is_shift_active` di Store Settings | 🔲 Belum |
| Phase 3B | Modifikasi `resto-cashier.php` (System Lock + Shift Methods) | 🔲 Belum |
| Phase 3C | Modifikasi `resto-cashier.blade.php` (UI Kondisional) | 🔲 Belum |
| Phase 3D | Partial Blade Views (Modal Buka/Tutup Shift & Kas Keluar) | 🔲 Belum |
| Phase 4 | Laporan & History Z-Report (Manager) | ⏸ Ditunda |

---

## Phase 1: Database Migration & Model ✅ SELESAI

### Migrasi yang sudah dieksekusi:
- `create_shifts_table` — tabel `shifts` (sesi kasir, uang laci)
- `create_shift_expenses_table` — tabel `shift_expenses` (kas keluar harian)
- `create_stock_opnames_table` — tabel `stock_opnames` (sesi opname)
- `create_stock_opname_items_table` — tabel `stock_opname_items` (detail opname, polymorphic)
- `add_is_critical_to_product_variants_table`
- `add_is_critical_to_raw_materials_table`
- `add_is_shift_active_to_store_settings_table`
- `refactor_variant_recipes_to_polymorphic_recipes` — tabel `recipes` polymorphic

### Model yang sudah ada:
- `app/Tenant/Models/Core/Shift.php` — constants `STATUS_ACTIVE`, `STATUS_CLOSED`
- `app/Tenant/Models/Core/ShiftExpense.php`
- `app/Tenant/Models/Core/StockOpname.php` — constants `STATUS_DRAFT`, `STATUS_COMPLETED`
- `app/Tenant/Models/Core/StockOpnameItem.php` — relasi `morphTo()`
- `app/Tenant/Models/Resto/VariantRecipe.php` — diubah ke `$table = 'recipes'`, `morphTo()`
- `app/Tenant/Models/Core/ProductVariant.php` — tambah `is_critical`, relasi `morphMany()`
- `app/Tenant/Models/Resto/RawMaterial.php` — tambah `is_critical`, relasi `HasMany`
- `app/Tenant/Models/Resto/ProductExtra.php` — tambah relasi `morphMany()` ke recipes

### Struktur Tabel `shifts`:
```
id | user_id | started_at | ended_at | starting_cash | cash_sales
cash_expenses | expected_cash | actual_cash | difference | status | note
```

### Struktur Tabel `stock_opname_items`:
```
id | stock_opname_id | opnameable_type | opnameable_id
system_stock | physical_stock | difference | note
```

---

## Phase 2A: OrderService Refactor ✅ SELESAI

### Yang sudah diimplementasikan:
- `processRevenue()` — saat transaksi `cash` + `is_shift_active = true`, uang masuk ke `shifts.cash_sales` (bukan langsung ke `Wallet::TYPE_CASH`)
- `processRefund()` — saat cancel/void transaksi `cash`, kurangi `cash_sales` di shift aktif
- Auto-deduct `RawMaterial` via `ProductExtra` resep polymorphic (topping)
- Helper `getWalletTypeForPayment()` — mapping `cash → TYPE_CASH`, `qris/transfer → TYPE_BANK`, `duitku/midtrans → TYPE_GATEWAY`

---

## Phase 2B: Multi-Wallet (BillingService + TenantWalletService) ✅ SELESAI

### Yang sudah diimplementasikan:
- `Wallet::TYPE_BILLING`, `TYPE_CASH`, `TYPE_BANK`, `TYPE_GATEWAY`
- `TenantWalletService::getWallet(string $type)` — multi-wallet per tipe
- `BillingService::chargeTransactionFee()` — guard `application_fee > 0` + `isGatewaySettlement`
- Migration `add_type_and_name_to_wallets_table` + backfill wallet lama ke `billing`

---

## Phase 2C: ShiftService 🔲 BELUM

### Lokasi:
`app/Tenant/Services/ShiftService.php`

### Standar implementasi:
- Ikuti **Thin Controller + Fat Service** pattern
- Gunakan **DB Transaction manual** (`try/catch`, `beginTransaction/commit/rollBack`)
- Named Arguments di semua pemanggilan internal

### Method yang akan dibangun:

#### `openShift(int $userId, float $startingCash): Shift`
```
Guard: Cek apakah user sudah punya shift aktif (Shift::STATUS_ACTIVE). Jika ada, throw Exception.
Action:
  1. Shift::create([user_id, started_at: now(), starting_cash, status: active])
  2. Return shift baru
```

#### `addExpense(Shift $shift, float $amount, string $description): ShiftExpense`
```
Guard: Shift harus berstatus active. Throw jika closed.
Guard: amount > 0. Throw jika nol atau negatif.
Action:
  1. DB::beginTransaction
  2. ShiftExpense::create([shift_id, amount, description])
  3. $shift->increment('cash_expenses', $amount)
  4. DB::commit
  5. Return ShiftExpense
```

#### `initiateClose(Shift $shift): array`
```
Tidak menulis DB. Hanya menyiapkan data untuk form opname:
  1. Query RawMaterial::where('is_critical', true)->select(['id', 'name', 'unit', 'stock'])->get()
  2. Return array dengan format: [['id', 'name', 'unit', 'system_stock', 'physical_stock' => null, 'note' => null]]
```

#### `closeShift(Shift $shift, float $actualCash, array $opnameItems): Shift`
```
Guard: Shift harus berstatus active. Throw jika sudah closed.
Action:
  1. DB::beginTransaction
  2. Hitung expected_cash = starting_cash + cash_sales - cash_expenses
  3. Hitung difference = actual_cash - expected_cash
  4. Buat StockOpname::create([shift_id, user_id, status: completed])
  5. Loop opnameItems → buat StockOpnameItem (polymorphic ke RawMaterial)
     - Hitung difference = physical_stock - system_stock
     - Jika difference < 0 (kurang) → RawMaterial::decrement('stock', abs(difference))
     - Jika difference > 0 (lebih) → RawMaterial::increment('stock', difference)
  6. $shift->update([status: closed, ended_at: now(), actual_cash, expected_cash, difference])
  7. TenantWalletService::addBalance(amount: actualCash, reference: shift, description: ..., walletType: Wallet::TYPE_CASH)
  8. DB::commit
  9. Return shift
```

---

## Phase 2D: DTO untuk Shift 🔲 BELUM

### File yang akan dibuat:

#### `app/Tenant/Data/ShiftExpenseData.php`
```php
class ShiftExpenseData extends Data
{
    public function __construct(
        public float $amount,
        public string $description,
    ) {}
}
```

#### `app/Tenant/Data/ShiftOpnameItemData.php`
```php
class ShiftOpnameItemData extends Data
{
    public function __construct(
        public int $rawMaterialId,
        public float $physicalStock,
        public ?string $note = null,
    ) {}
}
```

#### `app/Tenant/Data/ShiftClosingData.php`
```php
class ShiftClosingData extends Data
{
    public function __construct(
        public float $actualCash,
        /** @var ShiftOpnameItemData[] */
        public array $opnameItems,
    ) {}
}
```

---

## Phase 3A: Toggle `is_shift_active` di Store Settings 🔲 BELUM

### File terdampak:

#### Step 1: `app/Tenant/Data/StoreSettingFormData.php`
- Tambah field `public bool $isShiftActive`

#### Step 2: `app/Tenant/Services/SettingService.php`
- Di method `saveFromForm()`, tambah mapping: `'is_shift_active' => $data->isShiftActive`

#### Step 3: `resources/views/pages/tenant/setting/⚡store-setting/store-setting.php`
- Tambah public property: `public bool $isShiftActive = false`
- Di `mount()`, assign dari `$setting->is_shift_active ?? false`
- Di `save()`, sertakan dalam DTO yang dikirim ke `SettingService`

#### Step 4: `resources/views/pages/tenant/setting/⚡store-setting/store-setting.blade.php`
- Tambah UI toggle (checkbox/switch) untuk `is_shift_active`
- Tempatkan di bagian "Pengaturan Kasir" atau berdekatan dengan toggle Dapur (`is_kitchen_active`)
- Gunakan `x-model="$wire.isShiftActive"` + `wire:model="isShiftActive"` (Zero-Roundtrip pattern)
- Tampilkan deskripsi kontekstual: "Kasir wajib membuka sesi shift sebelum bisa menerima pesanan"

---

## Phase 3B: Modifikasi `resto-cashier.php` 🔲 BELUM

### Tambahan pada komponen Livewire:

#### Import & Lazy Getter:
```php
use App\Tenant\Models\Core\Shift;
use App\Tenant\Services\ShiftService;

protected ?ShiftService $shiftService = null;

protected function shiftService(): ShiftService
{
    return $this->shiftService ??= app(ShiftService::class);
}
```

#### Public Properties (State Shift):
```php
public bool $isShiftActive = false;

// Modal: Buka Shift
public float $startingCash = 0;

// Modal: Kas Keluar
public float $expenseAmount = 0;
public string $expenseDescription = '';

// Modal: Tutup Shift (Step 1 Opname)
public array $opnameItems = [];

// Modal: Tutup Shift (Step 2 Kas Fisik)
public float $actualCash = 0;
```

#### Computed Property:
```php
#[Computed]
public function activeShift(): ?Shift
{
    if (!$this->isShiftActive) return null;
    return Shift::where('user_id', Auth::id())
        ->where('status', Shift::STATUS_ACTIVE)
        ->first();
}
```

#### Modifikasi `mount()`:
```php
$storeSetting = StoreSetting::cached();
$this->isShiftActive = (bool)($storeSetting?->is_shift_active ?? false);
```

#### Methods:
```php
public function openShift(): void
{
    $this->validate(['startingCash' => ['required', 'numeric', 'min:0']]);
    try {
        $this->shiftService()->openShift(userId: Auth::id(), startingCash: $this->startingCash);
        $this->startingCash = 0;
        unset($this->activeShift); // reset computed cache
        $this->toast('Shift berhasil dibuka!');
    } catch (Exception $e) {
        $this->toast($e->getMessage(), 'danger');
    }
}

public function addExpense(): void
{
    $this->validate([
        'expenseAmount' => ['required', 'numeric', 'min:1'],
        'expenseDescription' => ['required', 'string', 'max:255'],
    ]);
    try {
        $this->shiftService()->addExpense(
            shift: $this->activeShift,
            amount: $this->expenseAmount,
            description: $this->expenseDescription
        );
        $this->expenseAmount = 0;
        $this->expenseDescription = '';
        unset($this->activeShift);
        $this->toast('Pengeluaran berhasil dicatat.');
    } catch (Exception $e) {
        $this->toast($e->getMessage(), 'danger');
    }
}

public function prepareCloseShift(): void
{
    $this->opnameItems = $this->shiftService()->initiateClose(shift: $this->activeShift);
}

public function closeShift(): void
{
    $this->validate(['actualCash' => ['required', 'numeric', 'min:0']]);
    try {
        $this->shiftService()->closeShift(
            shift: $this->activeShift,
            actualCash: $this->actualCash,
            opnameItems: $this->opnameItems
        );
        $this->actualCash = 0;
        $this->opnameItems = [];
        unset($this->activeShift);
        $this->toast('Shift berhasil ditutup. Laporan Z-Report tersimpan.');
    } catch (Exception $e) {
        $this->toast($e->getMessage(), 'danger');
    }
}
```

#### Modifikasi `with()`:
- Tambah `'isShiftActive' => $this->isShiftActive` dan `'activeShift' => $this->activeShift` ke return array

---

## Phase 3C: Modifikasi `resto-cashier.blade.php` 🔲 BELUM

### Perubahan UI:

#### System Lock (Buka Shift):
```blade
@if($isShiftActive && !$this->activeShift)
    {{-- Overlay penuh, sembunyikan konten POS --}}
    @include('pages.tenant.pos.partials._modal-open-shift')
@else
    {{-- Konten POS normal --}}
    <div class="pos-shell ...">
        ...
    </div>
@endif
```

#### Indikator Shift Aktif di Navbar/Header:
```blade
@if($isShiftActive && $this->activeShift)
    <div class="flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">
        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
        Shift: {{ $this->activeShift->cashier->name }} · {{ $this->activeShift->started_at->format('H:i') }}
    </div>
@endif
```

#### Tombol Kas Keluar & Tutup Shift (di area Cart/Sidebar):
```blade
@if($isShiftActive && $this->activeShift)
    <button @click="isShiftExpenseModalOpen = true">Kas Keluar</button>
    <button @click="initiateCloseShift()">Tutup Shift</button>
@endif
```

Catatan: Semua modal state (`isShiftExpenseModalOpen`, `isCloseShiftModalOpen`, dll) dikelola Alpine — tidak ada `wire:model.live`.

---

## Phase 3D: Partial Blade Views 🔲 BELUM

### File yang akan dibuat:

#### `resources/views/pages/tenant/pos/partials/_modal-open-shift.blade.php`
```
- Tampilan: Layar penuh atau modal tengah
- Konten:
  - Judul: "Buka Sesi Kasir"
  - Deskripsi: "Masukkan jumlah uang tunai yang tersedia di laci kasir saat ini."
  - Input: "Modal Laci (Rp)" — numeric, wire:model="startingCash"
  - Tombol: "Mulai Shift" — wire:click="openShift()" + wire:loading.attr="disabled"
```

#### `resources/views/pages/tenant/pos/partials/_modal-shift-expense.blade.php`
```
- Tampilan: Modal overlay (x-show)
- Konten:
  - Judul: "Catat Kas Keluar"
  - Input: "Nominal (Rp)" — numeric, wire:model="expenseAmount"
  - Input: "Keterangan" — text, wire:model="expenseDescription", placeholder: "contoh: Beli es batu"
  - Tombol: "Catat" — wire:click="addExpense()" + wire:loading
  - Tombol: "Batal" — @click="isShiftExpenseModalOpen = false"
```

#### `resources/views/pages/tenant/pos/partials/_modal-close-shift.blade.php`
```
- Tampilan: Modal overlay besar, 2-step
- Step 1 (Opname Stok):
  - Judul: "Opname Stok — Bahan Kritis"
  - Deskripsi: "Hitung fisik bahan baku berikut dan masukkan jumlah yang tersisa."
  - Loop $opnameItems:
    - Nama + Satuan (readonly)
    - Stok Sistem (readonly, pre-fill dari system_stock)
    - Input Stok Fisik — wire:model="opnameItems.{index}.physical_stock"
    - Input Alasan (opsional) — wire:model="opnameItems.{index}.note", placeholder: "contoh: tumpah"
  - Tombol: "Lanjut → Hitung Kas" — @click="closeShiftStep = 2"

- Step 2 (Hitung Kas Fisik — BLIND):
  - Judul: "Hitung Uang di Laci"
  - PENTING: Tidak menampilkan expected_cash ke kasir (anti-fraud)
  - Deskripsi: "Hitung total uang tunai yang ada di laci kasir sekarang dan masukkan nilainya."
  - Input: "Total Uang di Laci (Rp)" — numeric, wire:model="actualCash"
  - Tombol: "Tutup Shift & Simpan" — wire:click="closeShift()" + wire:loading
  - Tombol: "← Kembali" — @click="closeShiftStep = 1"
```

---

## Phase 4: Laporan Z-Report ⏸ DITUNDA

Ditunda sampai Phase 3 selesai dan stabil.

Rencana awal:
- Halaman `/shift-report` atau sub-tab di dashboard Manager
- Tampilkan rekap per shift: nama kasir, jam, total penjualan cash, pengeluaran, selisih uang, selisih stok
- Grafik sederhana waste per bahan baku (opsional)

---

## Aturan Standarisasi (Wajib Diikuti)

### Laravel 13:
- `#[Fillable([...])]` attribute di atas class (bukan `$fillable`)
- `protected function casts(): array` (bukan `$casts`)
- Constants domain di model (bukan raw string)
- DB Transaction: `try/catch` + `beginTransaction/commit/rollBack` (bukan closure)
- Single-line if tanpa `{}`
- Named Arguments untuk method dengan parameter banyak
- Service Injection: Lazy Getter (bukan `app()` inline)
- DTO (`Spatie\LaravelData\Data`) untuk semua data Livewire → Service

### Livewire 4:
- Zero-Roundtrip: `x-model="$wire.prop"` + `wire:model="prop"` untuk toggle (bukan `wire:model.live`)
- `#[Computed]` untuk data yang di-query tapi tidak perlu disimpan di state
- `rules()` method (bukan `#[Validate]` untuk form kompleks)
- Dirty indicator via `$wire.$dirty()` (bukan Alpine flag manual)
- Modal state dikelola Alpine (`x-show`), bukan Livewire

### Urutan Eksekusi:
```
Step 1  → ShiftExpenseData.php
Step 2  → ShiftOpnameItemData.php
Step 3  → ShiftClosingData.php
Step 4  → ShiftService.php (openShift, addExpense, initiateClose, closeShift)
Step 5  → StoreSettingFormData.php (tambah isShiftActive)
Step 6  → SettingService.php (tambah mapping)
Step 7  → store-setting.php + blade (toggle UI)
Step 8  → resto-cashier.php (inject, computed, methods)
Step 9  → _modal-open-shift.blade.php
Step 10 → _modal-shift-expense.blade.php
Step 11 → _modal-close-shift.blade.php
Step 12 → resto-cashier.blade.php (integrasi UI kondisional)
Step 13 → pint + lint check
Step 14 → php artisan tenants:migrate
```
