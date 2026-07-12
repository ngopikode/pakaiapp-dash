# Plan: POS Kasir — Navbar & Fixed Layout (no page scroll)

## Konteks
User mau halaman kasir (resto) jadi fixed viewport F&B POS: product grid kiri, receipt kanan, navbar berisi `PakaiApp POS` + tanggal/jam realtime + `Total: X Orders` + switch `Kasir / Open Bill`. Tidak boleh `routeIs('cashier')` (flicker gara-gacr wire:poll/navigate) — data navbar di-pass dari Livewire page. Kontrol POS yang tadinya di body (`mb-3 flex...`) dipindah ke navbar.

Bug sekarang setelah eksekusi pertama:
1. Halaman kasir masih bisa di-scroll & cart kepotong → `pos-shell` height salah + `main` masih `p-4 md:p-6` (double offset) + pakai `100vh` bukan `100dvh`.
2. Harga produk kepotong → card `h-[230px]` fix + image gede + price ga `truncate`.
3. Plan no 2 (navbar 3 zona kiri/tengah/kanan) belum pas ditaruh → tengah & kanan numpuk, mobile ga ada switch.

## Target file (edit)
1. `resources/views/layouts/app.blade.php`
2. `resources/views/components/layouts/⚡navbar/navbar.blade.php`
3. `resources/views/components/layouts/⚡navbar/navbar.php`
4. `resources/views/pages/tenant/pos/⚡index/index.php`
5. `resources/views/pages/tenant/pos/⚡resto-cashier/resto-cashier.blade.php`
6. `resources/views/pages/tenant/pos/⚡resto-cashier/resto-cashier.css` (opsional kecil)
7. `resources/views/components/tenant/pos/⚡product-list/product-list.blade.php`
8. `resources/views/pages/tenant/pos/partials/_cart-resto.blade.php`

---

## 1. Layout POS fixed (app.blade.php)
`main` untuk POS jadi `p-0 overflow-hidden`:
```blade
<main class="w-full @if($showSidebar) p-4 md:p-6 @else @endif @if(is_array($navbar ?? null) && ($navbar['mode'] ?? null) === 'pos') !p-0 overflow-hidden @endif">
```
Gunakan `$navbar` (array) bukan `$title` biar deteksi mode POS konsisten.

`pos-shell` height fix dvh (resto-cashier.blade.php line 1):
```blade
<div class="pos-shell mt-2 mb-2 h-[calc(100dvh-72px)] overflow-hidden rounded-[2rem] bg-[#F5F2EA] px-5 pb-5 pt-3 text-slate-900 dark:bg-slate-950 dark:text-slate-100 lg:px-6 lg:pb-6 lg:pt-4 ..." ...>
```
Mobile (navbar 64px): override via class `max-lg:h-[calc(100dvh-64px)]`.
Inner tab container (line 43) jadi `h-[calc(100%-0px)]` cukup (pos-shell sudah dikurangi navbar), atau pakai `h-full`:
```blade
<div x-show="currentTab === 'cashier'" ... class="flex h-full min-h-0 flex-col gap-5 overflow-hidden lg:flex-row" ...>
```
Queue (line 53): `min-h-[calc(100%-1rem)] overflow-y-auto` -> `h-full overflow-y-auto`.

Hasil: hanya product grid & cart-items yang scroll; halaman ga scroll.

## 2. Navbar 3 zona (navbar.blade.php) — NO 2
Struktur: `flex items-center justify-between gap-3` sudah ada. Isi:
- KIRI: toggle + (mobile title) + POS title+tanggal (lg only) — sudah ada, pertahankan.
- TENGAH (POS only): `Total: X Orders` + switch `Kasir / Open Bill` + help.
- KANAN: theme + notif + user (sudah ada `ul`).

Tengah block ganti jadi:
```blade
@if($isPosNavbar)
    <div class="flex flex-1 items-center justify-center gap-3 lg:justify-center"
         x-data="{ tab: 'cashier', change(v){ this.tab=v; window.dispatchEvent(new CustomEvent('pos-change-tab',{detail:v})) } }"
         @force-cashier-tab.window="tab='cashier'">
        <span class="hidden text-sm font-bold text-emerald-800/80 dark:text-emerald-400/80 sm:inline">Total: {{ $navbar['pendingOrders'] ?? $this->pendingOrdersCount }} Orders</span>
        <div class="inline-flex rounded-full bg-slate-100 p-1 shadow-inner dark:bg-slate-900">
            <button type="button" @click="change('cashier')" class="rounded-full px-4 py-2 text-sm font-black transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" :class="tab==='cashier' ? 'bg-white text-emerald-800 shadow-sm dark:bg-slate-800 dark:text-emerald-400' : 'text-slate-600 hover:text-emerald-800 dark:text-slate-300 dark:hover:text-emerald-400'">Kasir</button>
            <button type="button" @click="change('queue')" class="relative rounded-full px-4 py-2 text-sm font-black transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" :class="tab==='queue' ? 'bg-white text-emerald-800 shadow-sm dark:bg-slate-800 dark:text-emerald-400' : 'text-slate-600 hover:text-emerald-800 dark:text-slate-300 dark:hover:text-emerald-400'">Open Bill @if(($navbar['pendingOrders'] ?? $this->pendingOrdersCount) > 0)<span class="absolute -right-1 -top-1 rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] text-white">{{ $navbar['pendingOrders'] ?? $this->pendingOrdersCount }}</span>@endif</button>
        </div>
        <button id="tour-pos-help" type="button" @click="window.dispatchEvent(new CustomEvent('force-cashier-tab')); setTimeout(()=>window.dispatchEvent(new CustomEvent('start-pos-tour')),300)" class="hidden h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-emerald-800 transition hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 sm:flex dark:bg-slate-900 dark:text-emerald-400 dark:hover:bg-slate-800" title="Panduan & Tutorial Penggunaan"><i class="ph-bold ph-lightbulb text-lg"></i></button>
    </div>
@endif
```
Mobile: tengah tetap di navbar (flex-1 justify-center), `Total:` disembunyikan di <sm, switch tetap kelihatan. Tidak lagi di body POS.

## 3. Data navbar (index.php + navbar.php)
`index.php` tambah `pendingOrders`:
```php
public array $navbar = [
    'mode' => 'pos',
    'title' => 'PakaiApp POS',
    'pendingOrders' => \App\Tenant\Models\Core\Order::where('status','pending')->count(),
];
```
(navbar.php sudah punya `pendingOrdersCount` computed, jadi fallback aman.)

## 4. Product card harga ga kepotong (product-list.blade.php)
- Hapus `h-[230px]` fix → `min-h-[210px]`.
- Image: `h-[118px]` → `h-24 w-full` (lebih kecil, ga makan place harga).
- Price row (line 123-131): `whitespace-nowrap`, price `truncate`, plus button `h-9 w-9 text-xl`.
```blade
<div class="mt-auto flex items-end justify-between gap-2 pt-3">
    <p class="mb-0 truncate whitespace-nowrap text-sm font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($displayPrice,0,',','.') }}</p>
    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-emerald-800 bg-white text-xl text-emerald-800 shadow-sm ..."><i class="ph-bold ph-plus"></i></div>
</div>
```

## 5. Cart ga kepotong (_cart-resto.blade.php)
Kecilkan padding header/form/summary biar area scroll (`#tour-cart-items`) dapet tempat:
- Header (line 9): `px-6 py-6` → `px-4 py-3`.
- Form grid (line 38): `px-6 pb-4` → `px-4 pb-3`; input `py-3` → `py-2.5`.
- Order-type buttons (line 29): `py-2.5` → `py-2`.
- Summary box (line 100): `p-4` → `p-3`.
- Tombol bawah (line 121/124): `py-4` → `py-3`.
Cart container (`flex h-full min-h-0 flex-col overflow-hidden`) sudah benar; `#tour-cart-items` `flex-1 overflow-y-auto` sudah benar → footer total + tombol tetap di bawah, ga kepotong.

## 6. Event sync (sudah ada, pastikan)
resto-cashier.blade.php line 20:
```blade
@pos-change-tab.window="if($event.detail === 'cashier' && isEditingOrder) window.location.href='/cashier'; else currentTab = $event.detail"
```
Tetap. Navbar dispatch `pos-change-tab`.

## Verifikasi
- `npm run build` (asset CSS/JS baru).
- `php -l` file PHP Volt yg diubah.
- Cek visual: kasir ga scroll halaman, cart footer kelihatan, harga produk utuh, navbar: kiri brand+tanggal, tengah total+switch, kanan user.
- Tidak ubah logic/event lain.
