# Fitur: Store Setting UI — Tailwind Rewrite + Section Jam Operasional

> Referensi: [Project Map](../../project-map.md) · [Operating Hours Plan](../operating-hours/plan.md) · [Tailwind Migration Plan](../tailwind-migration/PLAN.md)

Dokumen ini mencatat rencana rewrite halaman `/store-setting` dari Bootstrap ke Tailwind CSS, sekaligus menambahkan section Jam Operasional sebagai Fase 4 dari fitur Operating Hours.

---

## Konteks & Masalah Saat Ini

1. **Bootstrap di tengah Tailwind** — `store-setting.blade.php` masih pakai Bootstrap (`form-control`, `btn`, `card`, `col-md-*`, `d-flex`). Layout shell (`app.blade.php`), sidebar, dan halaman lain seperti `dashboard`, `user-profile`, `wallet` sudah full Tailwind.
2. **CSS lokal** — `store-setting.css` berisi custom class Bootstrap-dependent (`.premium-input`, `.premium-setting-card`, `.setting-switch`) yang tidak kompatibel dengan dark mode Tailwind.
3. **Mobile kurang** — Tab nav horizontal pakai `overflow-x: auto` manual, tidak responsive secara proper. Sidebar tab tidak natural di mobile.
4. **Tab Jam Operasional belum ada** — Field `operating_hours` dan `use_same_hours` sudah ada di DB dan model (Operating Hours Fase 1), tapi belum ada UI untuk mengeditnya di halaman setting.

---

## Keputusan Arsitektur

### 1. Layout: Sidebar Tab → Scroll Section ✅ Keputusan Final

**Sidebar tab dihapus. Tidak ada opsi lain.**

Ganti pola sidebar tab Alpine (`tab: 'basic'`) dengan **scroll section vertikal** — setiap section adalah satu block dengan heading kiri + card kanan.

Ikuti pola `user-profile.blade.php` yang sudah ada: `grid-cols-1 md:grid-cols-12`, deskripsi 4 col kiri, form card 8 col kanan.

**Alasan:**
- Mobile: sidebar tab horizontal overflow tidak natural, scroll section jauh lebih intuitif
- Tidak perlu Alpine state `{ tab: 'basic' }` sama sekali → lebih sedikit JS, tidak ada potensi bug
- Konsisten dengan semua halaman Tailwind yang sudah ada (`user-profile`, `dashboard`)
- Section bisa diberi anchor `id` untuk deep-link di masa depan

**Pola yang dihapus** (tidak boleh ada di implementasi):
```blade
{{-- DILARANG — jangan pakai pola ini --}}
<div x-data="{ tab: 'basic' }">
    <button @click="tab = 'basic'">...</button>
    <div x-show="tab === 'basic'">...</div>
</div>
```

### 2. Zero Server Roundtrip untuk Interaksi

Semua interaksi UI (toggle, input) adalah **pure client-side**. Satu-satunya server call adalah saat tombol Simpan ditekan.

| Interaksi | Mekanisme | Server? |
|-----------|-----------|---------|
| Toggle `use_same_hours` | `wire:model` — sync saat save | ❌ |
| Toggle `is_closed` per hari | Alpine: `$wire.operating_hours.day.is_closed = !value` (JS mutation, no request) | ❌ |
| Input `open` / `close` jam | `wire:model` biasa — sync saat save | ❌ |
| Greyed-out saat libur | `x-bind:disabled` + `x-bind:class` berdasarkan `$wire.operating_hours.day.is_closed` — pure Alpine | ❌ |
| Toggle pajak/SC show/hide | `x-show="$wire.is_tax_active"` — sudah ada, tetap pure Alpine | ❌ |
| Simpan semua | `wire:click="save"` — 1 roundtrip untuk semua data sekaligus | ✅ 1x |

> **Larangan keras**: Tidak ada `wire:model.live`, `wire:change`, `$wire.set()`, `$wire.$refresh()`, atau tab-switching Alpine di halaman ini. Semua sync terjadi saat save. Semua show/hide berdasarkan `$wire.*` — reactive client-side tanpa roundtrip.

### 3. Hapus `store-setting.css`

Semua style akan pakai Tailwind utilities langsung di blade. File `.css` dikosongkan. Tidak ada custom CSS baru.

### 4. Icon: Bootstrap Icons → Phosphor Icons

Sesuai Tailwind Migration Fase 7. Ganti `bi bi-*` → `ph-*` (Phosphor) yang sudah dipakai di `dashboard.blade.php` dan layout.

### 5. Input Waktu: Native `<input type="time">`

Tidak perlu library picker. Browser native time input sudah cukup dan accessible.

---

## Struktur UI Baru

### Mobile (< md) — single column scroll
```
┌─ Header ──────────────────────────────────┐
│  "Pengaturan Toko"  [Simpan Perubahan →]  │
└───────────────────────────────────────────┘

┌─ Section: Info Dasar ─────────────────────┐
│  Nama toko, warna tema                    │
│  Upload logo                              │
│  WhatsApp, alamat                         │
│  Tipe toko (disabled), status buka/tutup  │
│  KDS toggle (jika resto)                  │
│  Metode pesanan: dine-in/takeaway/delivery│
│  Pajak PB1 + Service Charge (jika resto)  │
└───────────────────────────────────────────┘

┌─ Section: Jam Operasional ────────────────┐
│  Toggle: "Jam sama untuk semua hari"      │
│                                           │
│  [Jika sama = true]                       │
│  ┌─ Semua Hari ──────────────────────┐   │
│  │  Buka [08:00]  Tutup [22:00]      │   │
│  │  [ ] Libur hari ini               │   │
│  └───────────────────────────────────┘   │
│                                           │
│  [Jika sama = false]                      │
│  ┌─ Senin ───────────────────────────┐   │
│  │  Buka [08:00]  Tutup [22:00]  [ ]│   │
│  └───────────────────────────────────┘   │
│  ... (Selasa s/d Minggu)                  │
│  Saat libur: input disabled + opacity-50  │
└───────────────────────────────────────────┘

┌─ Section: Hero & Navbar ──────────────────┐
│  Navbar brand text, title, subtitle       │
│  Hero headline, tagline, promo, status    │
│  Link Instagram                           │
└───────────────────────────────────────────┘

┌─ Section: SEO & Meta ─────────────────────┐
│  SEO title, description, keywords         │
│  OG title, description, image upload      │
└───────────────────────────────────────────┘
```

### Desktop (≥ md) — grid 4:8
```
[Icon + Judul Section    ] [─────── Form Card ────────────────────]
[+ deskripsi singkat     ] [  fields...                           ]
[                        ] [  footer: tidak ada, save di header   ]
──────────────────────────────────────────────────────────────────
[Icon + Judul Section    ] [─────── Form Card ────────────────────]
...
```

---

## File yang Diubah

| File | Perubahan |
|------|-----------|
| `resources/views/pages/tenant/setting/⚡store-setting/store-setting.blade.php` | Full rewrite Bootstrap → Tailwind. Layout scroll-section. Tambah section Jam Operasional. Icon → Phosphor. |
| `resources/views/pages/tenant/setting/⚡store-setting/store-setting.php` | Tambah props `use_same_hours` + `operating_hours`. Update `mount()` + `save()`. |
| `resources/views/pages/tenant/setting/⚡store-setting/store-setting.css` | Kosongkan semua isi (hapus class Bootstrap-dependent). |

**Tidak ada file baru. Tidak ada route baru. Tidak ada migration baru.**

---

## Props Baru (`store-setting.php`)

```php
public bool $use_same_hours = false;
public array $operating_hours = [];
```

`mount()` — load dengan fallback defaults 7 hari:
```php
$days    = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
$default = ['open' => '08:00', 'close' => '22:00', 'is_closed' => false];
$loaded  = $setting->operating_hours ?? [];

$this->use_same_hours  = (bool)($setting->use_same_hours ?? false);
$this->operating_hours = array_merge(
    ['default' => $loaded['default'] ?? $default],
    array_combine($days, array_map(fn($d) => $loaded[$d] ?? $default, $days))
);
```

`save()` — tambahkan ke `$data`:
```php
'use_same_hours'  => $this->use_same_hours,
'operating_hours' => $this->operating_hours,
```

---

## Pola Alpine untuk Jam Operasional

Toggle `is_closed` per hari — mutasi JS langsung, tidak trigger server request:
```blade
<input type="checkbox"
    x-model="$wire.operating_hours.monday.is_closed">

<input type="time"
    wire:model="operating_hours.monday.open"
    x-bind:disabled="$wire.operating_hours.monday.is_closed"
    x-bind:class="{ 'opacity-50 cursor-not-allowed': $wire.operating_hours.monday.is_closed }">
```

Toggle `use_same_hours` — juga tidak live, sync saat save:
```blade
<input type="checkbox" wire:model="use_same_hours">

<div x-show="!$wire.use_same_hours">  <!-- 7 rows hari -->
<div x-show="$wire.use_same_hours">   <!-- 1 row default -->
```

> `x-show` berdasarkan `$wire.use_same_hours` sudah reactive client-side tanpa roundtrip karena Alpine membaca properti Livewire via proxy `$wire`.

---

## Nama Hari (Indonesia)

```php
// Mapping untuk label tampilan
$dayLabels = [
    'monday'    => 'Senin',
    'tuesday'   => 'Selasa',
    'wednesday' => 'Rabu',
    'thursday'  => 'Kamis',
    'friday'    => 'Jumat',
    'saturday'  => 'Sabtu',
    'sunday'    => 'Minggu',
];
```

Label di blade pakai `@foreach` di blade — tidak di PHP component, karena hanya untuk display.

---

## Fase Implementasi

### Fase 1: Rewrite Blade + CSS ✅ Selesai

- [x] Kosongkan `store-setting.css`
- [x] Rewrite `store-setting.blade.php` → Tailwind, layout scroll-section
- [x] Section Info Dasar (existing content, ganti style + icon)
- [x] Section Hero & Navbar (existing content, ganti style + icon)
- [x] Section SEO & Meta (existing content, ganti style + icon)
- [x] Sticky/top save button, mobile-friendly

### Fase 2: Section Jam Operasional ✅ Selesai

- [x] Tambah props + `mount()` + `save()` di `store-setting.php`
- [x] Tambah section Jam Operasional di blade
- [x] Toggle `use_same_hours` + conditional show/hide Alpine
- [x] Row "Semua Hari" (`operating_hours.default`)
- [x] 7 rows Senin–Minggu dengan `x-bind:disabled` saat libur
- [x] Verifikasi: save → cache bust → `isOpenNow()` reflect perubahan

---

## Keterkaitan dengan Fitur Lain

| Fitur | Keterkaitan |
|-------|-------------|
| **Operating Hours** | Fase 4 (UI Settings) — setelah plan ini selesai, tandai ✅ di `operating-hours/plan.md` |
| **Tailwind Migration** | Bagian dari Fase 7 (Halaman Lain) di `tailwind-migration/PLAN.md` |
| **project-map.md** | Tambah entry feature ini ke tabel Features setelah implementasi selesai |

---

*Dibuat: 2026-07-27. Diperbarui seiring perkembangan implementasi.*
