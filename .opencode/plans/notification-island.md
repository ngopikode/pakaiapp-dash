# Plan: Notif Island + Kasir Navbar Cleanup + Theme Toggle

## Konteks (5 isu)
1. **Notif bell** → ganti `wire:poll` ke island + poll terisolasi (sudah disetujui sebelumnya)
2. **Kasir: sembunyikan notif bell** — di halaman kasir notif ga perlu, hapus dari DOM
3. **Kasir: pindah theme toggle** — jangan di posisi navbar sekarang (bareng notif+user), taruh di tempat lain yang lebih accessible buat cashier
4. **Off-canvas sidebar di 1024×768** — sudah di-handle (`showDesktopSidebar` default false <1280px, sidebar `hidden lg:flex` + w-0). Cek sekali lagi tidak ada gap/bug.
5. **Open Bill badge** — sudah ada `$this->pendingOrdersCount` di tombol Open Bill navbar. Tetap, tidak diubah.

## Target file
1. `resources/views/components/layouts/⚡navbar/navbar.blade.php`
2. `resources/views/layouts/app.blade.php` (off-canvas sidebar, sudah fix — verifikasi)

## 1. Notif bell → Livewire Island + poll terisolasi (perubahan navbar.blade.php)

Tarik seluruh notif `<li>` (lines 77-131) keluar dari `<ul>`, bungkus dengan `@island(name: 'notification-badge', defer: true)`.

Struktur baru di navbar:
```
<div class="flex items-center gap-1.5 lg:gap-3">
    @if(!$isPosNavbar)
        @island(name: 'notification-badge', defer: true)
            @placeholder → skeleton bell button
            <div x-data="{ open: false }" class="relative" wire:poll.20s.visible>
                ... bell + dropdown ...
            </div>
        @endisland
    @endif

    <ul class="flex items-center gap-1.5 lg:gap-3 m-0 p-0 list-none">
        @if(!$isPosNavbar)
            <li>theme toggle</li>
        @endif
        <li>user dropdown</li>
    </ul>
</div>
```

### Detail perubahan:
- **Hapus** `wire:poll.15s.visible` dari navbar (line 80) → tidak ada full-navbar poll
- **Notif bell** `@if(!$isPosNavbar)` → hidden saat kasir
- **Theme toggle** `@if(!$isPosNavbar)` → hidden dari `<ul>` saat kasir (dipindah, lihat poin 3)
- **`wire:poll.20s.visible`** → pasang di root island div → cuma badge notif yang re-render
- **`defer: true`** → query tidak jalan saat initial page load
- **`@placeholder`** → skeleton bell icon (lihat pattern `wallet.blade.php`)
- `user dropdown` tetap di `<ul>` semua mode

## 2. Theme Toggle → pindah ke POS center zone (kasir)

Pada mode `$isPosNavbar`, theme toggle dipindah dari `<ul>` ke zona tengah navbar (setelah tombol help). Tambahkan button di dalam `@if($isPosNavbar)` center div.

```blade
@if($isPosNavbar)
    <div class="flex flex-1 items-center justify-center gap-3" ...>
        <span>Total: ...</span>
        <div>Kasir / Open Bill</div>
        <button id="tour-pos-help">help</button>
        <button x-data="{ theme: localStorage.getItem('theme') || 'light' }"
                @click="theme = theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('theme', theme); document.documentElement.classList.toggle('dark', theme === 'dark')"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 transition hover:bg-slate-200 focus:outline-none dark:bg-slate-900 dark:hover:bg-slate-800"
                title="Ganti Tema">
            <i x-show="theme === 'dark'" class="ph-fill ph-sun text-[18px] text-orange-400" x-cloak></i>
            <i x-show="theme === 'light'" class="ph-fill ph-moon text-[18px] text-slate-500" x-cloak></i>
        </button>
    </div>
@endif
```

Di luar kasir (`!$isPosNavbar`): theme toggle tetap di `<ul>` (posisi asli).

## 3. Kasir: sembunyikan notif bell

Notif bell + dropdown + island → **seluruhnya** dibungkus `@if(!$isPosNavbar)` → tidak render di DOM saat kasir. Bersih.

## 4. Off-canvas sidebar di 1024×768

Sudah fix dari perubahan sebelumnya:
- `app.blade.php`: `showDesktopSidebar: window.innerWidth >= 1280 && ...` → false di <1280px
- Sidebar: `hidden lg:flex` + Alpine `:class="showDesktopSidebar ? 'w-64' : 'w-0 opacity-0 overflow-hidden'"` → 0px width di default
- Toggle button: `hidden lg:block` → visible di 1024px, klik buka sidebar off-canvas

**Verifikasi**: test di 1024×768 → sidebar default hidden, toggle buka, slide in. Tidak ada gap/bug. Sudah selesai, tidak perlu perubahan tambahan.

## 5. Open Bill badge

Sudah benar, tidak diubah:
```blade
@if($this->pendingOrdersCount > 0)
    <span class="absolute -right-1 -top-1 ...">26</span>
@endif
```
Badge ini tetap pakai `$this->pendingOrdersCount` dari navbar. Update saat island poll atau navigasi.

## Urutan eksekusi
1. Edit `navbar.blade.php`: hapus poll lama, wrap notif+dropdown dalam island + `@if(!$isPosNavbar)`, pindah theme toggle ke POS center zone
2. `php -l` navbar.php, `npm run build`
3. Verifikasi visual: kasir (notif hidden, theme di center), non-kasir (notif island live, theme di ul)

## Risiko / catatan
- `<li>` notif ditarik keluar `<ul>` → HTML valid karena island wrapper `<div>` jadi sibling flex
- Island `defer: true` → ada jeda singkat sebelum notif muncul (acceptable)
- POS center "Total: X Orders" tetap update saat navigasi, bukan live (accept, atau tambah event di island → follow-up)
