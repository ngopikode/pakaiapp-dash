# Fitur: Product SPA Modal Form — Tailwind & Zero-Roundtrip

> Referensi: [Project Map](../../project-map.md) · [Livewire 4 Patterns](../../references/livewire4/PATTERNS.md) · [Laravel 13 Patterns](../../references/laravel13/PATTERNS.md)

Dokumen ini mencatat rencana migrasi manajemen menu/produk menjadi Single Page Application (SPA) pada rute `/products`, menggunakan komponen Modal/Bottom Sheet Tailwind, serta menghilangkan ketergantungan pada routing halaman (create/edit) konvensional dan Bootstrap.

---

## Konteks & Masalah Saat Ini

1. **Routing Terpisah** — Halaman manajemen produk saat ini tersebar di 3 route: index (`/product`), create (`/product/create`), dan edit (`/product/{product}/edit`). Memperlambat navigasi dan memberikan UX yang kurang "app-like".
2. **Ketergantungan Bootstrap** — `form.blade.php` masih memuat class Bootstrap (form-control, row, col, btn) di tengah-tengah codebase yang sudah menggunakan Tailwind. Terdapat juga custom CSS (`form.css`) yang mungkin sudah tidak relevan.
3. **Standar Route** — Route admin masih singular (`product`). Route public menu juga menggunakan prefix `product.*`, berpotensi rancu secara semantik.

---

## Keputusan Arsitektur

### 1. Standarisasi Route

- **Admin CRUD:** Hanya akan ada satu route index plural:
  ```php
  Route::view('products', 'pages.tenant.product.product')->name('products');
  ```
- **Hapus Route Form:** `product.create` dan `product.edit` dihapus.
- **Route Public:** Tetap dipertahankan, namun dipindah namespace ke `menu.product.show` dan `menu.product.story` agar terpisah jelas dari admin.

**File terdampak:**
- `routes/tenant.php`
- Update nama route di berbagai link internal (`dashboard`, `tour-guide`, dll) dari `product` ke `products`.

### 2. Livewire Parent Component (SPA Wrapper)

- **Komponen Index:** Akan mengontrol state (menampilkan form, ID produk yang diedit).
- **Properti State:**
  ```php
  public bool $showForm = false;
  public ?int $editingProductId = null;
  public int $formKey = 0; // Remount force
  ```
- **Event Handling:** `$this->dispatch('product-saved')` pada form akan memicu `closeForm()` di index dan mereset `$showForm`.

**File terdampak:**
- `resources/views/pages/tenant/product/⚡index/index.php`
- `resources/views/pages/tenant/product/⚡index/index.blade.php` (menambahkan container modal Tailwind)

### 3. Tailwind Drawer / Bottom Sheet

UI Form akan dibungkus dalam Alpine.js modal wrapper.
- **Desktop:** Menampilkan Sliding Drawer (slide ke kiri) atau Centered Modal besar.
- **Mobile:** Menampilkan Bottom Sheet (slide dari bawah) yang menutup di bagian atas.
- **Kode Wrapper:** (Disisipkan di bawah `index.blade.php`)
  ```blade
  <div x-data="{ showForm: @entangle('showForm') }">
      <!-- Backdrop -->
      <div x-show="showForm" @click="$wire.set('showForm', false)" class="fixed inset-0 bg-black/50 z-40"></div>
      
      <!-- Drawer / Bottom Sheet -->
      <div x-show="showForm" 
           class="fixed bottom-0 md:top-0 md:bottom-auto md:right-0 z-50 w-full md:w-[800px] h-[90dvh] md:h-screen bg-white dark:bg-slate-900 shadow-2xl overflow-hidden flex flex-col transition-transform ...">
           @if($showForm)
               <livewire:pages::tenant.product.form :product-id="$editingProductId" :key="'form-'.$formKey" />
           @endif
      </div>
  </div>
  ```

### 4. Livewire Form: Zero-Roundtrip & Dirty Indicator

Implementasi patuh terhadap `PATTERNS.md`:
- **Mount Signature:** Berubah dari URL parameter ke argument ID manual (`mount(?int $productId = null)`).
- **Zero-Roundtrip Toggles:**
  Toggle "Punya Varian?" (dan toggle tab UI lainnya) TIDAK boleh menggunakan `wire:model.live`. Gunakan sinkronisasi Alpine:
  ```blade
  <input type="checkbox" x-model="$wire.hasVariants" wire:model="hasVariants">
  ```
- **Dirty Indicator:**
  Tombol simpan harus merespons `$wire.$dirty()`, menampilkan warna berbeda/badge peringatan jika ada form yang telah diubah namun belum disimpan.
  ```blade
  <button :class="$wire.$dirty() ? 'bg-orange-500 ring-4' : 'bg-slate-800'">Simpan</button>
  ```
- **Full Tailwind:** Semua markup HTML `form.blade.php` ditulis ulang menggunakan Tailwind (utility classes).

**File terdampak:**
- `resources/views/pages/tenant/product/⚡form/form.php`
- `resources/views/pages/tenant/product/⚡form/form.blade.php`
- `resources/views/pages/tenant/product/⚡form/form.css` (Dikosongkan/Dihapus)

---

## Fase Implementasi

### Fase 1: Standarisasi Route
- [ ] Ubah nama route admin index menjadi `products` di `routes/tenant.php`.
- [ ] Hapus rute `product.create` dan `product.edit`.
- [ ] Ubah name route public menjadi `menu.product.show` & `menu.product.story`.
- [ ] Ganti referensi `route('product')` menjadi `route('products')` pada file `dashboard`, `tour-guide`, dll.

### Fase 2: Parent Component (Index) Integration
- [ ] Tambahkan properti state (`showForm`, `editingProductId`, `formKey`) dan metode (`createProduct`, `editProduct`, `closeForm`) ke `index.php`.
- [ ] Sisipkan kontainer Alpine Tailwind Drawer/Bottom Sheet pada `index.blade.php`.
- [ ] Ubah tombol action di `index.blade.php` (tambah/edit) agar memanggil fungsi Livewire (`wire:click="createProduct"`) alih-alih berpindah halaman.

### Fase 3: Livewire Form Refactor
- [ ] Ubah signature `mount(?int $productId = null)` pada `form.php`.
- [ ] Ganti event redirect setelah `save()` dengan `$this->dispatch('product-saved')`.
- [ ] Kosongkan `form.css`.

### Fase 4: Form UI Tailwind Rewrite
- [ ] Render ulang seluruh layout `form.blade.php` menggunakan Tailwind.
- [ ] Susun struktur dengan Header yang sticky, Body yang scrollable, dan Footer (dengan tombol Simpan) yang sticky di bawah.
- [ ] Terapkan logika Zero-Roundtrip UI untuk input toggle/tabs menggunakan perpaduan `x-model` dan `wire:model`.
- [ ] Terapkan `$wire.$dirty()` pada tombol Simpan dan text indikator perubahan yang belum disimpan.