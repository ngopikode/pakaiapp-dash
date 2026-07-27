# UX: Save Pattern — Dirty Indicator + Mobile Polish

> Referensi: [Store Setting UI Plan](./plan.md) · [Project Map](../../project-map.md)
> Sumber: [Livewire 4 wire:dirty docs](../../references/livewire4/html-directives/wire-dirty.md) · [Livewire 4 forms docs](../../references/livewire4/essentials/forms.md)

## Masalah

1. **Tidak ada sinyal unsaved changes** — user edit field, scroll ke section lain, tidak ada indikator bahwa ada perubahan yang belum disimpan. Terasa kurang "native".
2. **`pb-24` berlaku di desktop** — container punya `pb-24` permanen padahal sticky bottom bar hanya muncul di mobile (`sm:hidden`). Desktop dapat 96px bottom padding sia-sia.
3. **iOS safe area** — sticky bottom bar bisa tertutup home indicator bar di iPhone (Safari). Perlu `env(safe-area-inset-bottom)`.

---

## Keputusan Arsitektur: Tetap Satu Tombol Save Global

Per-section save ditolak:
- Semua 4 section menyimpan ke satu row `store_settings`
- Per-section = 4× handler, 4× loading state, 4× toast, user bisa lupa save section lain
- Overhead tidak sebanding

Auto-save on blur ditolak:
- ~20 field di halaman = 20 roundtrip potensial per sesi edit
- Cross-field logic (`is_tax_active` + `tax_rate`, `use_same_hours` + per-day hours) tidak aman di-save setengah-setengah
- **Plan `store-setting-ui/plan.md` secara eksplisit melarang `wire:model.live`**

**Referensi**: GitHub Settings, Vercel project settings, Linear workspace — semua pakai scroll-section + satu global save.

---

## Fix #1: Dirty Indicator via `$wire.$dirty()`

### Sumber: Livewire 4 Docs

**`wire-dirty.md`** (offline docs):

```blade
{{-- Cek apakah SEMUA property di komponen berubah --}}
<div wire:show="$dirty">You have unsaved changes</div>

{{-- Apply class conditionally di Alpine --}}
<input wire:model="email" :class="$wire.$dirty('email') && 'border-yellow-500'">

{{-- $wire.$dirty() tanpa argumen = cek seluruh komponen --}}
<button :class="$wire.$dirty() ? 'ring-2 ring-orange-300' : ''">Save</button>
```

**Penting**: `$dirty` dan `$wire.$dirty()` bekerja dengan `wire:model` biasa (tanpa `.live`). Livewire track perubahan client-side antara state server dan state lokal — tidak butuh roundtrip untuk mendeteksi dirty.

### Implementasi

Tidak perlu `x-data` baru. Tidak perlu Alpine custom flag. Cukup tambahkan `:class` di kedua tombol Save:

**Tombol desktop (di header):**
```blade
<button wire:click="save"
        class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2"
        :class="$wire.$dirty() ? 'ring-2 ring-white/60 ring-offset-2 ring-offset-orange-500' : ''"
        wire:loading.attr="disabled">
    <span wire:loading.remove wire:target="save">
        <i class="ph ph-check-circle text-lg"></i> Simpan Perubahan
    </span>
    <span wire:loading wire:target="save" class="flex items-center gap-2">
        <i class="ph ph-spinner animate-spin text-lg"></i> Menyimpan...
    </span>
</button>
```

**Tombol mobile sticky (di bottom bar):**
```blade
<button wire:click="save"
        class="w-full px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2"
        :class="$wire.$dirty() ? 'ring-2 ring-white/60 ring-offset-2 ring-offset-orange-500' : ''"
        wire:loading.attr="disabled">
    ...
</button>
```

Opsional: tambahkan badge "Ada perubahan" yang muncul saat dirty:
```blade
{{-- Di dalam sticky bottom bar, di atas tombol --}}
<p class="text-xs text-slate-500 dark:text-slate-400 text-center mb-2"
   wire:show="$dirty">Ada perubahan yang belum disimpan</p>
```

---

## Fix #2: `pb-24` hanya di mobile

```blade
{{-- BEFORE --}}
<div class="max-w-6xl mx-auto pb-24 px-4 sm:px-6 lg:px-8 mt-6 sm:mt-10 font-sans">

{{-- AFTER — pb-24 hanya di mobile, sm ke atas cukup pb-10 --}}
<div class="max-w-6xl mx-auto pb-24 sm:pb-10 px-4 sm:px-6 lg:px-8 mt-6 sm:mt-10 font-sans">
```

---

## Fix #3: iOS Safe Area di Sticky Bottom Bar

iPhone dengan home indicator (iPhone X ke atas di Safari) bisa memotong konten di bawah. Gunakan `env(safe-area-inset-bottom)`:

```blade
{{-- BEFORE --}}
<div class="fixed bottom-0 left-0 right-0 z-50 p-4 bg-white dark:bg-slate-900 shadow-lg sm:hidden border-t ...">

{{-- AFTER — padding bawah menyesuaikan safe area --}}
<div class="fixed bottom-0 left-0 right-0 z-50 px-4 pt-3 pb-[max(1rem,env(safe-area-inset-bottom))] bg-white dark:bg-slate-900 shadow-lg sm:hidden border-t ...">
```

`max(1rem, env(safe-area-inset-bottom))` memastikan minimal 1rem padding bahkan di device yang tidak support safe area.

---

## Ringkasan Perubahan

| Masalah | Fix | File |
|---------|-----|------|
| Tidak ada unsaved indicator | `:class="$wire.$dirty() ? 'ring-2 ...' : ''"` di tombol Save universal | `store-setting.blade.php` |
| Sidebar ikut scroll di semua halaman | Ubah `h-full` → `h-screen sticky top-0` di sidebar desktop | `layouts/app.blade.php` |
| Tombol Save hilang saat scroll | Jadikan sticky bottom bar universal (desktop + mobile) + backdrop-blur | `store-setting.blade.php` |
| `pb-24` tidak cukup | Ubah ke `pb-28` di semua ukuran | `store-setting.blade.php` |
| iOS safe area terpotong | `pb-[max(1rem,env(safe-area-inset-bottom))]` di sticky bar | `store-setting.blade.php` |

**Tidak ada file PHP baru. Tidak ada roundtrip baru. Tidak ada dependency baru.**

---

## Fase Implementasi

### ✅ Fix #1: Sidebar Sticky

- [x] Ubah `h-full` → `h-screen sticky top-0` di sidebar desktop `layouts/app.blade.php`

### ✅ Fix #2: Universal Sticky Save Bar

- [x] Hapus tombol Save dari header desktop
- [x] Buat sticky bottom bar universal (hapus `sm:hidden`)
- [x] Efek `backdrop-blur-xl` agar konten di bawah tetap terlihat
- [x] Layout responsive: desktop text kiri + button kanan, mobile stacked
- [x] `pb-24 sm:pb-10` → `pb-28` (karena bar sekarang ada di semua ukuran)

### ✅ Fix #3: Dirty Indicator

- [x] Tambah `:class="$wire.$dirty() ? 'ring-2 ring-orange-500/50 ring-offset-2 ring-offset-white dark:ring-offset-slate-900' : ''"` di tombol Save universal
- [x] Teks "Ada perubahan yang belum disimpan" dengan `opacity` transisi (bukan `wire:show`)

---

## Catatan Teknis

### Kenapa `$wire.$dirty()` bukan Alpine custom flag

Sebelum riset docs, rencana awal menggunakan:
```blade
{{-- ❌ SALAH — tidak perlu ini --}}
<div x-data="{ dirty: false }" x-on:change.window="dirty = true">
```

Setelah baca `wire-dirty.md`:
- `$wire.$dirty()` adalah API bawaan Livewire 4, tidak butuh Alpine state tambahan
- Livewire track dirty state otomatis dari semua `wire:model` binding
- Bekerja **tanpa `wire:model.live`** — deteksi di client-side saja, tidak butuh roundtrip
- Lebih akurat: reset otomatis setelah `save()` berhasil dan server sync

### Kapan `$wire.$dirty()` return `false` lagi?

Setelah `wire:click="save"` berhasil → server mengirim response → Livewire sync state → `$dirty` reset ke `false` → ring hilang. Otomatis, tanpa kode tambahan.

---

*Dibuat: 2026-07-27. Sumber: `references/livewire4/html-directives/wire-dirty.md` + `references/livewire4/essentials/forms.md` line 599–622.*
