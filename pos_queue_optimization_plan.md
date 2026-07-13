# Implementation Plan: POS Queue Optimization (Livewire 4 @island)

## Goal Description
Mengoptimasi rendering pada bagian "Pesanan Ditahan" (Queue) agar tidak membebani parent component (`RestoCashier`) setiap kali kasir melakukan interaksi (seperti menambah keranjang). Sesuai dengan dokumentasi terbaru **Livewire 4**, kita akan memanfaatkan fitur **`@island`** yang memungkinkan kita untuk mengisolasi sebagian UI agar bisa di-render dan di-refresh secara mandiri tanpa perlu membuat komponen anak (*child component*) baru.

## User Review Required
> [!IMPORTANT]
> Fitur `@island` di Livewire 4 memberikan performa setara dengan memisahkan komponen anak, namun tanpa *overhead* pembuatan file class baru atau pusing mengatur komunikasi *prop*. Ketika parent merender ulang (misal: saat cart berubah), blok `@island` akan di-skip. Sebaliknya, ketika island dipicu (misal: refresh queue), hanya island tersebut yang merender ulang!

## Open Questions
> [!NOTE]
> Karena `@island` membatasi akses variabel lokal dari *parent template* (seperti variabel dari `return view([...])`), data pesanan (`$pendingOrders`) **harus** dipastikan berasal dari method `#[Computed]` atau properti publik (yakni diakses lewat `$this->pendingOrders`). Apakah `$pendingOrders` di `RestoCashier` sudah berupa `#[Computed]`? Jika belum, kita harus mengubahnya.

---

## Proposed Changes

### 1. Modifikasi View `resto-cashier.blade.php`
Bungkus bagian queue dengan direktif `@island` agar terisolasi dari *render cycle* utama. Kita juga akan menambahkan `wire:island="queue"` pada tombol refresh agar tindakan tersebut hanya berdampak pada island.

```diff
-        <div
-            class="sticky top-0 z-10 mb-4 flex items-center justify-between ...">
-            <div>
-                <h5 class="mb-0 text-lg font-black ...">Pesanan Ditahan</h5>
-                <p class="...">Open bill yang belum selesai</p>
-            </div>
-            <button type="button" wire:click="$refresh" ...>
-                ...
-            </button>
-        </div>
-
-        @include('pages.tenant.pos.partials._queue-resto')

+        @island(name: 'queue')
+            <div class="h-full">
+                <div
+                    class="sticky top-0 z-10 mb-4 flex items-center justify-between ...">
+                    <div>
+                        <h5 class="mb-0 text-lg font-black ...">Pesanan Ditahan</h5>
+                        <p class="...">Open bill yang belum selesai</p>
+                    </div>
+                    {{-- Gunakan wire:island agar action ter-scope hanya pada island ini --}}
+                    <button type="button" wire:click="$refresh" wire:island="queue" ...>
+                        ...
+                    </button>
+                </div>
+
+                {{-- Include partials, pastikan partial ini memanggil $this->pendingOrders (computed prop) --}}
+                @include('pages.tenant.pos.partials._queue-resto')
+            </div>
+        @endisland
```

### 2. Modifikasi Class `RestoCashier.php` (Jika `$pendingOrders` belum di-cache via Computed)
Pastikan pengambilan data `$pendingOrders` menggunakan atribut `#[Computed]`, bukan di *passing* langsung dari `render()`.
Alasannya: `#[Computed]` dievaluasi secara otomatis (on-demand) *hanya* jika island dirender, sehingga jika parent merender ulang, *query* database untuk antrean pesanan tidak akan dieksekusi secara sia-sia.

```php
use Livewire\Attributes\Computed;

// ...

#[Computed]
public function pendingOrders()
{
    return Order::where('status', 'pending')
        // ... kondisi lainnya
        ->get();
}
```

### 3. Modifikasi View Parsial `_queue-resto.blade.php`
Ubah seluruh pemanggilan variabel `$pendingOrders` (jika tadinya menggunakan passing variabel) menjadi pemanggilan properti komponen:
```diff
- @if($pendingOrders->isEmpty())
+ @if($this->pendingOrders->isEmpty())

- @foreach($pendingOrders as $order)
+ @foreach($this->pendingOrders as $order)
```

---

## Verification Plan

### Manual Verification
1. Buka halaman Kasir Resto.
2. Tambahkan produk ke keranjang secara beruntun.
3. Buka tab "Network" di DevTools, pastikan response Livewire **tidak memuat** seluruh markup dari pesanan ditahan (ukuran response harus mengecil secara drastis).
4. Klik tombol "Refresh" pada tab Pesanan Ditahan, pastikan proses loading hanya berdampak pada island tersebut dan tidak merusak/me-reset status keranjang belanja yang sedang aktif di kasir.
