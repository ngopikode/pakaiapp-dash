# Implementation Plan: Product SPA Modal Form

> Referensi: [Livewire 4 Patterns](../../references/livewire4/PATTERNS.md) · [Laravel 13 Patterns](../../references/laravel13/PATTERNS.md) · [Plan Awal](./plan.md)

---

## Phase 0 — Audit & Baseline

**Tujuan:** Pastikan state codebase jelas sebelum sentuh kode.

- Semua perubahan tetap unstaged.
- File terdampak:
  - `resources/js/app.js`
  - `resources/views/pages/tenant/product/⚡index/index.php`
  - `resources/views/pages/tenant/product/⚡index/index.blade.php`
  - `resources/views/pages/tenant/product/⚡form/form.php`
  - `resources/views/pages/tenant/product/⚡form/form.blade.php`
  - `app/Tenant/Services/ProductService.php`
  - `app/Tenant/Data/ProductFormData.php` (baru)

**Output:** Daftar masalah final. Tidak ada perubahan kode.

---

## Phase 1 — Loading Indicator Saat Buka Modal

**Masalah:** `createProduct` / `editProduct` tidak trigger loading.

**Perbaikan:**
- `resources/js/app.js`: tambah `'createProduct', 'editProduct'` ke `heavyActions`.
- Tambah skeleton dalam drawer (target `createProduct, editProduct`).

**Acceptance:**
- Klik Add/Edit → loading skeleton di drawer.
- Form muncul setelah mount selesai.

---

## Phase 2 — Drawer Flow Index Component

**Masalah:** Drawer shell tanpa loading → blank saat render child.

**State di `index.php` (existing, cukup verifikasi):**
```php
public bool $showForm = false;
public ?int $editingProductId = null;
public int $formKey = 0;
```

**Methods (existing, cukup verifikasi):**
```php
public function createProduct(): void;
public function editProduct(int $id): void;
#[On('close-product-form')] public function closeForm(): void;
#[On('product-saved')] public function refreshAfterSave(): void;
```

**Drawer di `index.blade.php` (existing, cukup verifikasi):**
- `x-data="{ showForm: @entangle('showForm') }"`
- Backdrop + drawer shell.
- `livewire:pages::tenant.product.form` di dalam `@if($showForm)`.

**Acceptance:**
- Add/Edit tidak navigate.
- Drawer tidak blank.
- Close backdrop jalan.
- Save → close drawer + refresh list.

---

## Phase 3 — `form.php`: Thin Component

**Masalah:** Form masih fat — pegang logic DB sendiri.

**Perbaikan:**
- Tambah lazy getter `ProductService`:
```php
protected ?ProductService $productService = null;
protected function productService(): ProductService
{
    return $this->productService ??= app(ProductService::class);
}
```
- `save()` hanya mapping, validasi, call service, dispatch.
- Tambah `rules()` array lengkap (termasuk variants.*.price, stock, dll).
- Gunakan **DTO** untuk kirim data ke service: `ProductFormData`.

**Struktur DTO baru:**
```php
namespace App\Tenant\Data;

use Spatie\LaravelData\Data;

class ProductFormData extends Data
{
    public function __construct(
        public string $name,
        public int $categoryId,
        public ?string $description,
        public ?string $image,
        public bool $taxIncluded,
        public bool $isActive,
        public bool $hasVariants,
        public string $selectionType,
        public int $maxSelections,
        public float $baseCost,
        public float $basePrice,
        public int $baseStock,
        public int $baseMinStock,
        public string $baseSku,
        public array $baseRecipes,
        public array $variants,
        public array $extras,
    ) {}
}
```

**`save()` baru (ringkas):**
```php
public function save(): void
{
    $this->validate();
    $dto = ProductFormData::from($this->all());
    try {
        $this->productService()->saveFromForm($this->product, $dto);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Produk berhasil disimpan.']);
        $this->dispatch('product-saved');
    } catch (Throwable $e) {
        report($e);
        $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menyimpan produk.']);
    }
}
```

**Acceptance:**
- `save()` < 30 baris.
- DB logic pindah ke service.
- Error user-friendly.

---

## Phase 4 — `ProductService`: Business Logic

**Masalah:** Logic DB masih di form component.

**Method baru di `ProductService`:**
```php
public function saveFromForm(?Product $product, ProductFormData $data): Product
{
    return DB::transaction(function () use ($product, $data) {
        $product = Product::updateOrCreate(
            ['id' => $product?->id],
            $data->toArray() // exclude relation fields
        );
        $this->syncVariants($product, $data);
        $this->syncExtras($product, $data);
        return $product;
    });
}
```

**Private helpers:**
- `syncVariants(Product $product, ProductFormData $data): void`
- `syncDefaultVariant(Product $product, ProductFormData $data): void`
- `syncVariantRecipes(Variant $variant, array $recipes): void`
- `syncExtras(Product $product, ProductFormData $data): void`

**Aturan:**
- Service tidak dispatch event/notify.
- Service return typed `Product`.
- Variant/Extra orphan cleanup di service.

**Acceptance:**
- `form.php` tidak punya `DB::beginTransaction()`.
- Service bisa di-test tanpa Livewire.

---

## Phase 5 — Blade Zero-Roundtrip & DTO Integration

**Masalah:** Blade masih ada `wire:model.live`, toggle trigger network.

**Perbaikan di `form.blade.php`:**

| Element | Before | After |
|---------|--------|-------|
| Checkbox varian | `wire:model.live="hasVariants"` | `x-model="$wire.hasVariants" wire:model="hasVariants"` |
| `wire:model.live` lain | semua | ganti ke `wire:model` |
| Tabs | Alpine `tab` (sudah benar) | tetap |
| Dirty indicator | sudah ada | verifikasi `$wire.$dirty()` |
| Status switch | sudah benar | tetap |

**Aturan Zero-Roundtrip:**
- `wire:model.live` hanya untuk search/filter index (butuh query realtime).
- Form input: `wire:model` (deferred, no roundtrip).
- Toggle UI: `x-model="$wire.prop" wire:model="prop"` (Alpine instant, sync deferred).
- Tabs: Alpine `x-data="{ tab: 'general' }"`, no Livewire.

**Acceptance:**
- Toggle varian tidak hit network.
- Pindah tab tidak hit network.
- Input tidak hit network sampai save / add-remove row.
- Dirty indicator jalan.

---

## Phase 6 — Form UI Tailwind Cleanup

**Masalah:** Sisa Bootstrap class, CSS tidak terpakai.

**Perbaikan:**
- Pastikan `form.css` kosong (sudah fase 3).
- Tidak ada class Bootstrap (`form-control`, `btn`, `row`, `col-md-*`).
- Struktur: sticky header, scrollable body, sticky footer.
- Konsisten dengan index component (radius, shadow, warna).

**Acceptance:**
- Tidak ada Bootstrap class di file form (blade + php).
- UI responsif mobile ↔ desktop.

---

## Phase 7 — Verification

**Commands:**
```bash
php artisan route:list | grep product
php -l app/Tenant/Data/ProductFormData.php
php -l app/Tenant/Services/ProductService.php
php -l resources/views/pages/tenant/product/⚡form/form.php
```

**Browser check:**
- Buka `/products` → list muncul.
- Klik Add → drawer terbuka, loading skeleton, form muncul.
- Toggle varian → UI berubah, no network request (cek tab Network).
- Isi form, dirty indicator muncul.
- Save → drawer close, list refresh, toast sukses.
- Edit → drawer terbuka dengan data existing.
- Save edit → data terupdate di list.

**Acceptance:** Semua flow jalan tanpa error console.

---

## Phase 8 — Git Rule

- Tidak commit.
- Tidak push.
- Semua perubahan stay unstaged.
- Reset/commit/push hanya atas perintah eksplisit.

---

## Ringkasan Perubahan

| File | Aksi |
|------|------|
| `app/Tenant/Data/ProductFormData.php` | **Baru** — DTO untuk form input |
| `app/Tenant/Services/ProductService.php` | **Tambah** — `saveFromForm()`, `syncVariants()`, `syncExtras()` |
| `resources/views/pages/tenant/product/⚡form/form.php` | **Refactor** — thin component, DTO, lazy getter |
| `resources/views/pages/tenant/product/⚡form/form.blade.php` | **Edit** — zero-roundtrip, wire:model.live removal |
| `resources/views/pages/tenant/product/⚡index/index.blade.php` | **Edit minor** — loading skeleton |
| `resources/js/app.js` | **Edit minor** — heavyActions |
