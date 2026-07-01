# Phase 3: View & Asset Restructuring

> **Prasyarat:** Phase 1 (Housekeeping) sudah selesai. Phase 2 (Domain Separation) tidak menjadi blocker — bisa dikerjakan paralel.
> **Referensi:** Lihat [Architecture Plan](.docs/project/architecture-plan.md) untuk gambaran besar.
> **Skill:** `ponytail-audit` — audit over-engineering & kompleksitas
> **Estimasi:** 3–4 jam (4 sub-phase)
> **Risiko:** Sedang (views mudah di-revert, tapi `@include` references mudah terlewat)

---

## 1. Konteks: Konvensi Livewire 4

Sebelum mulai, pahami konvensi resmi Livewire 4 yang **TIDAK BOLEH diubah**:

| Konvensi | Penjelasan | Config |
|----------|-----------|--------|
| `⚡` prefix | High-voltage emoji prefix untuk SFC & MFC. Resmi Livewire 4. | `config/livewire.php` line 76: `'emoji' => true` |
| MFC (Multi-File Component) | `.php` (class) + `.blade.php` (view) dalam subfolder `⚡nama-komponen/` | `component_locations` + `component_namespaces` |
| SFC (Single-File Component) | Inline class di dalam `.blade.php`, tanpa subfolder | Same config |
| Namespace prefixes | `pages::` → `resources/views/pages/`, `layouts::` → `resources/views/layouts/`, `components::` → `resources/views/components/` | `config/livewire.php` line 34-38 |
| Component locations | Livewire cari komponen di `resources/views/components/` dan `resources/views/livewire/` | `config/livewire.php` line 18-21 |

### Cara Resolusi Komponen

```
Route::livewire('dashboard', 'pages::tenant.dashboard')
     ↓
Namespace "pages" → resources/views/pages/
Slug "tenant.dashboard" → tenant/⚡dashboard/dashboard.php (MFC)
                         atau tenant/dashboard.blade.php (SFC)

<livewire:layouts.sidebar>
     ↓
Namespace "layouts" → resources/views/layouts/
Slug "sidebar" → ⚡sidebar.blade.php (SFC)
                atau sidebar/sidebar.php (MFC)

<livewire:pages::tenant.order.order-list/>
     ↓
Namespace "pages" → resources/views/pages/
Slug "tenant.order.order-list" → tenant/order/⚡order-list/order-list.php (MFC)
```

---

## 2. Analisis Masalah

### 2.1 Inkonsistensi Organisasi (3 pola bercampur)

| Pola | Contoh | Masalah |
|------|--------|---------|
| **By feature** | `pages/tenant/pos/`, `pages/tenant/order/` | Bagus, tapi tidak konsisten |
| **By type** | `pages/tenant/store/`, `pages/tenant/retail/` | Memisahkan by "tipe toko", bukan fitur |
| **Flat** | `pages/tenant/⚡dashboard/`, `pages/tenant/⚡kitchen/` | Komponen di root tanpa grouping |

### 2.2 Duplikasi

| Duplikat | Lokasi | File Count |
|----------|--------|------------|
| Stat cards | `components/desktop/stat-card.blade.php` + `components/mobile/stat-card.blade.php` | 2 → 1 |
| Product list | `store/resto/⚡product-list/` + `store/retail/⚡product-list/` + `components/tenant/pos/⚡product-list/` | 3 komponen berbeda |
| Store modals | `store/resto/{option,checkout,history}-modal.blade.php` + `retail/modals/{option,checkout,history}-modal.blade.php` | 6 file hampir identik |

### 2.3 Typo & Dead Code

| Path | Masalah |
|------|---------|
| `pages/tenant/post/` | Typo — harusnya `pos/` |
| `components/⚡counter.blade.php` | Orphaned — tidak dipakai |
| `pages/auth/⚡logout/` | Orphaned — tidak dipakai |
| `pages/central/⚡register-tenant/` | Orphaned — tidak dipakai |
| `pages/tenant/store/retail/⚡product-list/` | Orphaned — tidak dipakai |
| `layouts/retail.blade.php` | Unused — retail belum aktif |
| `resources/css/app.css` | Empty file (0 bytes) |
| `resources/sass/welcome.scss` | Compiled tapi tidak pernah di-load |
| `public/css/welcome.css` | Standalone, bypass Vite |
| `components/pages/tenant/` | Empty directory |

### 2.4 Inline Styles Berlebihan

32 file blade punya `<style>` blocks. Prioritas extract (>50 baris):

| File | Estimasi Baris CSS |
|------|-------------------|
| `pages/tenant/pos/_pos-tour-guide.blade.php` | ~500 |
| `components/tenant/⚡ai-floating-chat.blade.php` | ~200 |
| `components/layouts/⚡navbar.blade.php` | ~150 |
| `components/layouts/⚡sidebar.blade.php` | ~100 |
| `pages/tenant/⚡kitchen/kitchen.blade.php` | ~150 |
| `pages/tenant/⚡dashboard/dashboard.blade.php` | ~150 |
| `pages/tenant/order/⚡order-list/order-list.blade.php` | ~100 |
| `welcome.blade.php` | ~70 |
| `login.blade.php` | ~50 |
| `register.blade.php` | ~50 |

### 2.5 Asset Inconsistency

| Masalah | Detail |
|---------|--------|
| CDN vs npm | Landing pages load Bootstrap via CDN, dashboard via npm |
| SweetAlert2 duplikat | CDN di landing pages, npm di dashboard |
| 3 CSS bundles + 1 static | Dashboard (`sass/app.scss`), Store (`css/store.css`), Welcome (`css/welcome.css`), Static (`public/css/welcome.css`) |

---

## 3. Aturan Organisasi yang Diusulkan

### Prinsip

1. **Organize by feature** — kelompokkan by fitur (pos, order, product), bukan by type (store, retail)
2. **Consistent depth** — MFC selalu pakai subfolder `⚡nama/`, SFC di root
3. **Single source of truth** — hapus duplikat
4. **Keep `⚡` prefix** — konvensi resmi Livewire 4

### Naming Convention

| Tipe | Pattern | Contoh |
|------|---------|--------|
| MFC | `⚡nama-komponen/nama-komponen.{php,blade.php}` | `⚡dashboard/dashboard.php` |
| SFC | `⚡nama-komponen.blade.php` | `⚡ai-engine-manager.blade.php` |
| Regular Blade | `nama-file.blade.php` (tanpa ⚡) | `index.blade.php` |
| Partial | `_nama-file.blade.php` (underscore) | `_cart-resto.blade.php` |

---

## 4. Sub-Phase 3A: Dead Code Cleanup (30 menit)

### 4.1 Hapus Orphaned Components

| File | Tipe | Alasan |
|------|------|--------|
| `components/⚡counter.blade.php` | SFC | Demo component, tidak dipakai |
| `pages/auth/⚡logout/logout.php` + `.blade.php` | MFC | Tidak direferensikan |
| `pages/central/⚡register-tenant/register-tenant.php` + `.blade.php` | MFC | Tidak direferensikan |
| `pages/tenant/store/retail/⚡product-list/product-list.php` + `.blade.php` | MFC | Tidak direferensikan |

### 4.2 Hapus Dead Assets

| File | Baris | Alasan |
|------|-------|--------|
| `resources/css/app.css` | 0 | Empty, tidak pernah di-`@vite()` |
| `resources/sass/welcome.scss` | 136 | Compiled tapi tidak pernah di-load |
| `public/css/welcome.css` | 692 | Standalone, bypass Vite |

### 4.3 Hapus Unused Layout

| File | Alasan |
|------|--------|
| `layouts/retail.blade.php` | Tidak dipakai. `retail/index.blade.php` pakai standalone HTML. Aktifkan lagi saat retail jadi. |

### 4.4 Hapus Empty Directories

| Path |
|------|
| `components/pages/tenant/` |
| `components/pages/` |
| `components/desktop/` (setelah merge ke `ui/`) |
| `components/mobile/` (setelah merge ke `ui/`) |

### 4.5 Update `vite.config.js`

```js
// SEBELUM (7 entry points)
input: [
    'resources/sass/app.scss',
    'resources/js/app.js',
    'resources/css/store.css',
    'resources/js/store.js',
    'resources/sass/welcome.scss',  // HAPUS — dead
    'resources/css/welcome.css',
    'resources/js/welcome.js',
    'resources/css/app.css',        // HAPUS — dead (jika ada)
],

// SESUDAH (5 entry points)
input: [
    'resources/sass/app.scss',
    'resources/js/app.js',
    'resources/css/store.css',
    'resources/js/store.js',
    'resources/css/welcome.css',
    'resources/js/welcome.js',
],
```

### Hasil 3A

- 4 orphaned components dihapus
- 3 dead assets dihapus
- 1 unused layout dihapus
- 4 empty directories dihapus
- Vite config dibersihkan (7 → 5 entry points)

---

## 5. Sub-Phase 3B: Fix Typo & Naming (30 menit)

### 5.1 Rename `post/` → `pos/`

Direktori `pages/tenant/post/` adalah typo.

| File Lama | File Baru |
|----------|----------|
| `pages/tenant/post/_queue-resto.blade.php` | `pages/tenant/pos/_queue-resto.blade.php` |
| `pages/tenant/post/_history-retail.blade.php` | `pages/tenant/pos/_history-retail.blade.php` |

### 5.2 Update `@include` References

| File | Lama | Baru |
|------|------|------|
| `pages/tenant/pos/⚡resto-cashier/resto-cashier.blade.php` | `@include('pages.tenant.post._queue-resto')` | `@include('pages.tenant.pos._queue-resto')` |
| `pages/tenant/pos/⚡retail-cashier/retail-cashier.blade.php` | `@include('pages.tenant.post._history-retail')` | `@include('pages.tenant.pos._history-retail')` |

### 5.3 Verifikasi Missing Component Files

Dua route mereferensikan komponen yang **tidak ada di disk** atau **mungkin SFC yang tidak resolve dengan benar**:

| Route | Component Name | File di Disk |
|-------|---------------|--------------|
| `routes/tenant.php:55` | `pages::tenant.mobile-menu` | `pages/tenant/⚡mobile-menu.blade.php` (SFC) |
| `routes/tenant.php:66` | `pages::tenant.ai-engine-manager` | `pages/tenant/⚡ai-engine-manager.blade.php` (SFC) |

**Action:** Test akses route ini. Jika 500 error, konversi SFC → MFC:
```
pages/tenant/⚡mobile-menu/  →  mobile-menu.php + mobile-menu.blade.php
pages/tenant/⚡ai-engine-manager/  →  ai-engine-manager.php + ai-engine-manager.blade.php
```

### Hasil 3B

- Typo `post/` → `pos/` fixed
- 2 `@include` references updated
- 2 missing component files diverifikasi/di-fixed

---

## 6. Sub-Phase 3C: Consolidate Duplicates (1 jam)

### 6.1 Merge Stat Cards

Gabungkan `components/desktop/stat-card.blade.php` + `components/mobile/stat-card.blade.php` → `components/ui/stat-card.blade.php`.

**Approach:** Gunakan Tailwind responsive classes (`md:`, `lg:`) untuk menangani perbedaan desktop vs mobile dalam satu file.

**Langkah:**
1. Buat `components/ui/stat-card.blade.php` — merge logic dari kedua file
2. Update semua references:
   - `<x-desktop.stat-card>` → `<x-ui.stat-card>`
   - `<x-mobile.stat-card>` → `<x-ui.stat-card>`
3. Hapus `components/desktop/` dan `components/mobile/`

**Cari references:**
```bash
grep -rn "x-desktop.stat-card" resources/views/
grep -rn "x-mobile.stat-card" resources/views/
```

### 6.2 Evaluasi Product List Triplication

Saat ini ada 3 komponen `product-list`:

| Lokasi | Dipakai oleh | Store Type |
|--------|-------------|------------|
| `pages/tenant/store/resto/⚡product-list/` | `<livewire:pages::tenant.store.resto.product-list/>` | Resto storefront |
| `pages/tenant/retail/⚡product-list/` | `<livewire:pages::tenant.retail.product-list/>` (commented out) | Retail storefront |
| `components/tenant/pos/⚡product-list/` | `<livewire:tenant.pos.product-list/>` | POS cashier |

**Decision:** Biarkan terpisah. Ketiganya punya konteks berbeda:
- Store resto = customer-facing, read-only menu
- Store retail = customer-facing, read-only menu (retail layout)
- POS = admin-facing, dengan add-to-cart logic

### 6.3 Evaluasi Store Modals Duplikasi

| Resto (`store/resto/`) | Retail (`retail/modals/`) |
|------------------------|--------------------------|
| `option-modal.blade.php` | `option-modal.blade.php` |
| `checkout-modal.blade.php` | `checkout-modal.blade.php` |
| `history-modal.blade.php` | `history-modal.blade.php` |

**Decision:** Biarkan terpisah. Resto dan retail punya flow checkout berbeda secara fundamental. Tandai sebagai tech debt, revisit saat retail store aktif.

### Hasil 3C

- Stat cards merged (2 → 1)
- Product list & store modals: dipertahankan (didokumentasikan sebagai intentional)
- Empty folders dihapus

---

## 7. Sub-Phase 3D: Inline Style Extraction (1–2 jam)

### 7.1 Strategy

| Source Layout | Target CSS Bundle |
|---------------|-------------------|
| Layouts: `app`, `central`, `guest`, `print` | `resources/sass/app.scss` |
| Layouts: `store`, `retail`, `mobile` | `resources/css/store.css` |
| Standalone: `welcome`, `login`, `register` | `resources/css/welcome.css` |

### 7.2 Aturan Extraction

1. **Hanya extract yang >50 baris.** Inline style kecil (<50 baris) yang spesifik per-komponen boleh tetap inline.
2. **Jangan extract yang dinamis** — CSS yang pakai Blade variable (e.g., `background: {{ $setting->theme_color }}`) tetap inline.
3. **Test setelah extract** — pastikan tidak ada specificity conflict.

### 7.3 Priority Queue

| Priority | File | Estimasi Baris | Target Bundle |
|----------|------|---------------|---------------|
| P1 | `pages/tenant/pos/_pos-tour-guide.blade.php` | ~500 | `store.css` atau `app.scss` |
| P2 | `components/tenant/⚡ai-floating-chat.blade.php` | ~200 | `store.css` |
| P3 | `components/layouts/⚡navbar.blade.php` | ~150 | `app.scss` |
| P4 | `pages/tenant/⚡kitchen/kitchen.blade.php` | ~150 | `app.scss` |
| P5 | `pages/tenant/⚡dashboard/dashboard.blade.php` | ~150 | `app.scss` |
| P6 | `components/layouts/⚡sidebar.blade.php` | ~100 | `app.scss` |
| P7 | `pages/tenant/order/⚡order-list/order-list.blade.php` | ~100 | `app.scss` |
| P8 | `welcome.blade.php` | ~70 | `welcome.css` |
| P9 | `login.blade.php` | ~50 | `welcome.css` |
| P10 | `register.blade.php` | ~50 | `welcome.css` |

### Hasil 3D

- ~1.500 baris inline CSS di-extract ke CSS bundles
- 10 file blade dibersihkan dari `<style>` blocks besar

---

## 8. Struktur Final Setelah Phase 3

```
resources/views/
├── layouts/                          # 6 layouts (retail dihapus)
│   ├── app.blade.php
│   ├── central.blade.php
│   ├── guest.blade.php
│   ├── mobile.blade.php
│   ├── print.blade.php
│   └── store.blade.php
│
├── components/
│   ├── ui/                           # Generic UI (merged)
│   │   └── stat-card.blade.php
│   ├── layouts/
│   │   ├── ⚡navbar.blade.php         # SFC
│   │   ├── ⚡sidebar.blade.php        # SFC
│   │   ├── sidebar-item.blade.php
│   │   ├── retail/
│   │   │   └── _navbar.blade.php
│   │   └── store/
│   │       └── _navbar.blade.php
│   ├── tenant/
│   │   ├── ⚡ai-floating-chat.blade.php
│   │   ├── order/
│   │   │   └── cancel-modal.blade.php
│   │   └── pos/
│   │       └── ⚡product-list/        # MFC
│   ├── ⚡central-ai-floating-chat.blade.php
│   ├── adaptive-stat-card.blade.php
│   ├── pwa-toast.blade.php
│   ├── tour-guide.blade.php
│   ├── tour-guide-accordion.blade.php
│   └── tour-guide-form.blade.php
│
├── pages/
│   ├── auth/
│   │   ├── ⚡login/
│   │   ├── ⚡forgot-password/
│   │   └── ⚡reset-password/
│   ├── central/
│   │   ├── ⚡central-admin/
│   │   └── ⚡topup-tenant/
│   └── tenant/
│       ├── ⚡dashboard/
│       ├── ⚡kitchen/
│       ├── ⚡ai-daily-briefing/
│       ├── ⚡ai-engine-manager.blade.php     # SFC
│       ├── ⚡mobile-menu.blade.php          # SFC
│       ├── pos/
│       │   ├── ⚡index/
│       │   ├── ⚡resto-cashier/
│       │   ├── ⚡retail-cashier/
│       │   ├── _cart-resto.blade.php
│       │   ├── _cart-retail.blade.php
│       │   ├── _queue-resto.blade.php       # moved from post/
│       │   ├── _history-retail.blade.php     # moved from post/
│       │   ├── _modal-payment.blade.php
│       │   ├── _modal-variant.blade.php
│       │   ├── _modal-success.blade.php
│       │   ├── _modal-option.blade.php
│       │   ├── _modal-merge-resto.blade.php
│       │   ├── _modal-held-orders.blade.php
│       │   ├── _modal-tutorial.blade.php
│       │   └── _pos-tour-guide.blade.php
│       ├── order/
│       │   ├── index.blade.php
│       │   ├── ⚡index/
│       │   ├── ⚡order-list/
│       │   │   └── _modal-split-bill.blade.php
│       │   ├── ⚡order-modal/
│       │   ├── ⚡payment-modal/
│       │   └── ⚡show/
│       ├── product/
│       │   ├── product.blade.php
│       │   ├── ⚡index/
│       │   ├── ⚡form/
│       │   └── ⚡category-modal/
│       ├── invoice/
│       │   └── ⚡show/
│       ├── payment/
│       │   ├── return.blade.php
│       │   └── ⚡wallet/
│       ├── profile/
│       │   └── ⚡user-profile/
│       ├── setting/
│       │   ├── ⚡store-setting/
│       │   └── ⚡buy-product-slot/
│       ├── user/
│       │   ├── index.blade.php
│       │   ├── ⚡user-list/
│       │   └── ⚡user-modal/
│       ├── resto/
│       │   └── raw-material.blade.php       # SFC
│       ├── store/
│       │   ├── resto/
│       │   │   ├── index.blade.php
│       │   │   ├── product.blade.php
│       │   │   ├── ⚡product-list/
│       │   │   ├── _hero.blade.php
│       │   │   ├── _loader.blade.php
│       │   │   ├── option-modal.blade.php
│       │   │   ├── checkout-modal.blade.php
│       │   │   └── history-modal.blade.php
│       │   └── retail/
│       │       └── (future)
│       └── retail/
│           ├── index.blade.php
│           ├── _hero.blade.php
│           ├── ⚡product-list/
│           └── modals/
│               ├── _loader.blade.php
│               ├── option-modal.blade.php
│               ├── checkout-modal.blade.php
│               └── history-modal.blade.php
│
├── emails/
│   └── system.blade.php
│
├── errors/
│   ├── layout.blade.php
│   ├── minimal.blade.php
│   └── *.blade.php (401-503)
│
├── welcome.blade.php
├── login.blade.php
├── register.blade.php
├── register-status.blade.php
└── tenant/
    └── story_preview.blade.php
```

---

## 9. Asset Bundle Final State

### Entry Points (5)

| Bundle | CSS Source | JS Source | Dipakai oleh |
|--------|-----------|----------|--------------|
| Dashboard | `resources/sass/app.scss` | `resources/js/app.js` | layouts: app, central, guest, print |
| Store | `resources/css/store.css` | `resources/js/store.js` | layouts: store, mobile; standalone: errors/minimal, retail/index, store/resto/product |
| Welcome | `resources/css/welcome.css` | `resources/js/welcome.js` | standalone: welcome, login, register |

### Yang Dihapus

| File | Alasan |
|------|--------|
| `resources/css/app.css` | Empty (0 bytes) |
| `resources/sass/welcome.scss` | Dead — compiled tapi tidak di-load |
| `public/css/welcome.css` | Standalone, bypass Vite |

---

## 10. Langkah Eksekusi

### Step 1: Backup
```bash
git add -A && git commit -m "checkpoint: before phase 3 view restructuring"
```

### Step 2: Dead Code Cleanup (3A)
```bash
# Hapus orphaned components
rm -rf resources/views/components/⚡counter.blade.php
rm -rf resources/views/pages/auth/⚡logout/
rm -rf resources/views/pages/central/⚡register-tenant/
rm -rf resources/views/pages/tenant/store/retail/⚡product-list/

# Hapus dead assets
rm resources/css/app.css
rm resources/sass/welcome.scss
rm public/css/welcome.css

# Hapus unused layout
rm resources/views/layouts/retail.blade.php

# Hapus empty directories
rm -rf resources/views/components/pages/
```

### Step 3: Update vite.config.js (3A)
Hapus entry points `resources/sass/welcome.scss` dan `resources/css/app.css` dari `input` array.

### Step 4: Fix Typo (3B)
```bash
# Move partials from post/ to pos/
mv resources/views/pages/tenant/post/_queue-resto.blade.php resources/views/pages/tenant/pos/
mv resources/views/pages/tenant/post/_history-retail.blade.php resources/views/pages/tenant/pos/
rmdir resources/views/pages/tenant/post/
```

### Step 5: Update @include references (3B)
Cari dan replace di blade files:
```bash
# Cari references
grep -rn "pages.tenant.post" resources/views/

# Replace
# pages.tenant.post._queue-resto → pages.tenant.pos._queue-resto
# pages.tenant.post._history-retail → pages.tenant.pos._history-retail
```

### Step 6: Merge Stat Cards (3C)
```bash
mkdir -p resources/views/components/ui
# Merge logic dari desktop + mobile ke ui/stat-card.blade.php
# Hapus originals
rm -rf resources/views/components/desktop/
rm -rf resources/views/components/mobile/
```

Update references:
```bash
grep -rn "x-desktop.stat-card" resources/views/
grep -rn "x-mobile.stat-card" resources/views/
# Replace dengan x-ui.stat-card
```

### Step 7: Extract Inline Styles (3D)
Untuk setiap file di tabel 7.3:
1. Baca file, identifikasi `<style>` block
2. Cut CSS content, paste ke CSS bundle yang sesuai
3. Hapus `<style>` tag dari blade file
4. Test halaman di browser

### Step 8: Clear Cache & Rebuild
```bash
php artisan optimize:clear
rm -rf storage/framework/views/*
npm run build
```

### Step 9: Verifikasi
```bash
# Cek tidak ada broken @include
grep -rn "pages.tenant.post" resources/views/   # harus 0 results
grep -rn "x-desktop.stat-card" resources/views/  # harus 0 results
grep -rn "x-mobile.stat-card" resources/views/    # harus 0 results

# Cek route masih jalan
php artisan route:list

# Rebuild assets
npm run build
```

---

## 11. Checklist Verifikasi

- [ ] 3A: Orphaned components dihapus (4 komponen)
- [ ] 3A: Dead assets dihapus (3 file)
- [ ] 3A: Unused layout dihapus (`retail.blade.php`)
- [ ] 3A: Empty directories dihapus
- [ ] 3A: `vite.config.js` dibersihkan (7 → 5 entry points)
- [ ] 3B: `post/` → `pos/` renamed
- [ ] 3B: `@include` references updated (2 files)
- [ ] 3B: Missing component files diverifikasi (`mobile-menu`, `ai-engine-manager`)
- [ ] 3C: Stat cards merged (2 → 1 → `components/ui/stat-card.blade.php`)
- [ ] 3C: Component references updated (`x-desktop.*` → `x-ui.*`, `x-mobile.*` → `x-ui.*`)
- [ ] 3C: Empty folders dihapus (`desktop/`, `mobile/`, `pages/`)
- [ ] 3D: Inline styles >50 baris di-extract (10 files, ~1.500 baris CSS)
- [ ] `npm run build` berhasil tanpa error
- [ ] `php artisan route:list` konsisten
- [ ] Akses halaman utama di browser — tidak ada broken layout
- [ ] Akses dashboard — tidak ada broken layout
- [ ] Akses store/POS — tidak ada broken layout

---

## 12. Rollback Plan

```bash
git checkout -- .
php artisan optimize:clear
rm -rf storage/framework/views/*
npm run build
```

---

## 13. Catatan Penting

1. **`⚡` prefix BUKAN masalah.** Ini konvensi resmi Livewire 4 (high-voltage emoji). Dikonfigurasi di `config/livewire.php`. Jangan hapus atau rename.

2. **Store modals duplikasi adalah intentional.** Resto dan retail punya flow checkout berbeda. Merge akan menambah complexity, bukan mengurangi.

3. **Inline style extraction opsional.** Jika waktu terbatas, skip 3D. Inline styles tidak broken, hanya kurang maintainable.

4. **`public/css/welcome.css` adalah pre-built static.** Landing pages pakai CDN Bootstrap + file ini. Jika dihapus, landing pages akan broken. Pastikan landing pages sudah migrasi ke Vite bundle sebelum hapus — atau biarkan.

5. **`pages::tenant.mobile-menu` dan `pages::tenant.ai-engine-manager`** — dua route yang komponennya SFC di root `pages/tenant/`. Bisa jadi resolve dengan benar via Livewire, atau bisa jadi bug. Test akses route ini untuk konfirmasi.

6. **Product list triplication adalah intentional.** Store resto (customer-facing), store retail (customer-facing), POS (admin-facing) punya konteks berbeda. Jangan merge.

---

> **Selanjutnya:** Setelah Phase 3 selesai, lanjut ke [Phase 4: Route & Config Cleanup](.docs/project/architecture-plan.md#phase-4-route--config-cleanup-12-jam).
