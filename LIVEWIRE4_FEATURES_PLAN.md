# Livewire 4 Feature Implementation Plan
## Target: `⚡product-list` (Halaman Utama Store Resto)

> **Scope**: Non-breaking refactor. Tidak ada perubahan UI/UX yang terlihat oleh end-user,
> kecuali peningkatan performa dan kemampuan share/bookmark URL.

---

## Phase 1 — `#[Url]` : Shareable State via Query String
**Effort:** Rendah (±10 menit) | **Impact:** Tinggi

### Konteks
Saat ini, jika user memfilter kategori "Ayam" lalu share URL-nya ke teman, temannya
akan melihat halaman kosong tanpa filter. Semua state (kategori, sort, search) hanya
hidup di memori Livewire, tidak di URL.

Dengan `#[Url]`, URL akan otomatis berubah menjadi:
```
/?kategori=Ayam&q=bakar&sort=newest
```
URL ini bisa di-share, di-bookmark, dan di-back-button browser.

### Keputusan Desain

**`viewMode` (grid/list) → `#[Session]` bukan `#[Url]`**
Preferensi tampilan grid vs list adalah preferensi personal user, bukan state yang ingin
di-share. Jika pakai `#[Url]`, URL jadi kotor dengan `?view=grid`.
Solusinya: `#[Session]` agar preferensi persisten per user tapi tidak tampil di URL.

| Property | Strategi | Alias URL | Dikecualikan saat |
|---|---|---|---|
| `$search` | `#[Url]` | `q` | value `''` |
| `$category` | `#[Url]` | `kategori` | value `'all'` |
| `$sort` | `#[Url]` | `sort` | value `'popular'` |
| `$minPrice` | `#[Url]` | `min` | value `null` |
| `$maxPrice` | `#[Url]` | `max` | value `null` |
| `viewMode` | `#[Session]` | — | (tidak di URL) |

### File yang Diubah

#### [MODIFY] product-list.php
```diff
+ use Livewire\Attributes\Session;
+ use Livewire\Attributes\Url;

  new class extends Component {
-     public bool $lazy = false;      // dihapus, sudah tidak dipakai

+     #[Url(as: 'kategori', except: 'all')]
      public string $category = 'all';

+     #[Url(as: 'q', except: '')]
      public string $search = '';

+     #[Url(as: 'sort', except: 'popular')]
      public string $sort = 'popular';

+     #[Url(as: 'min', except: null)]
      public ?int $minPrice = null;

+     #[Url(as: 'max', except: null)]
      public ?int $maxPrice = null;

+     #[Session]
+     public string $viewMode = 'grid'; // dipindah dari Alpine ke Livewire Session
  };
```

#### [MODIFY] product-list.blade.php
```diff
- x-data="{ viewMode: 'grid', showFilter: false }"
+ x-data="{ showFilter: false }"

  {{-- Toggle viewMode lewat wire agar tersimpan di session --}}
- @click="viewMode = 'list'"
+ wire:click="$set('viewMode', 'list')"

- @click="viewMode = 'grid'"
+ wire:click="$set('viewMode', 'grid')"

  {{-- Semua x-bind/:class yang pakai viewMode tetap sama karena Livewire expose $viewMode --}}
```

---

## Phase 2 — `@island.append` : Infinite Scroll yang Benar
**Effort:** Sedang (±45 menit) | **Impact:** Sangat Tinggi (Performa)

### Konteks
Ini adalah perubahan paling signifikan dari sisi performa.

**Masalah Sekarang:**
Setiap kali `loadMore()` dipanggil, Livewire me-render ulang seluruh komponen dan
mengirim SEMUA produk (dari item ke-1 sampai ke-N) ke browser. Ini pemborosan besar.

```
Request 1: kirim 10 produk   (OK)
Request 2: kirim 20 produk   (2x payload)
Request 3: kirim 30 produk   (3x payload — pemborosan!)
```

**Dengan `@island.append`:**
Setiap request hanya mengirim 10 produk baru dan langsung di-append ke DOM.

```
Request 1: kirim 10 produk   (OK)
Request 2: kirim 10 produk   (tetap 10, di-append)
Request 3: kirim 10 produk   (tetap 10, di-append — efisien!)
```

### Perubahan Arsitektur

Saat ini `loadMore()` bekerja dengan menaikkan `$perPage`, yang menyebabkan query
mengambil semua item dari awal sampai `$perPage`. Dengan Islands, kita ubah ke
model berbasis `$page`:

```php
// SEKARANG (kurang efisien — query makin besar setiap load more)
public int $perPage = 10;
public function loadMore(): void { $this->perPage += 10; }
// query: ->take($this->perPage)->get()  → semakin banyak

// SETELAH (benar — query tetap 10 item tiap request)
public int $page = 1;
public function loadMore(): void { $this->page++; }
// query: ->forPage($this->page, 10)->get()  → selalu 10 item
```

### File yang Diubah

#### [MODIFY] product-list.php
```diff
- public int $perPage = 10;
+ public int $page = 1;

  public function setCategory(string $category): void
  {
      $this->category = $category;
-     $this->perPage = 10;
+     $this->page = 1;
  }

  public function setSort(string $sort): void
  {
      $this->sort = $sort;
-     $this->perPage = 10;
+     $this->page = 1;
  }

  public function resetFilters(): void
  {
      ...
-     $this->perPage = 10;
+     $this->page = 1;
  }

  public function applyFilters(): void
  {
-     $this->perPage = 10;
+     $this->page = 1;
  }

- public function loadMore(): void { $this->perPage += 10; }
+ public function loadMore(): void { $this->page++; }

  #[Computed]
  public function hasMore(): bool
  {
-     return $this->getBaseProductQuery()->count() > $this->perPage;
+     return $this->getBaseProductQuery()->count() > ($this->page * 10);
  }

  #[Computed]
  public function products(): array
  {
      ...
-     return $query->take($this->perPage)->get()->map(...)->toArray();
+     return $query->forPage($this->page, 10)->get()->map(...)->toArray();
  }
```

#### [MODIFY] product-list.blade.php
```diff
  {{-- Bungkus loop produk dengan @island --}}
+ @island(name: 'products')
      @forelse ($this->products as $index => $item)
          {{-- kartu produk (tidak berubah) --}}
      @endforelse

      @empty
          {{-- empty state (tidak berubah) --}}
      @endempty
+ @endisland

  {{-- Ubah sentinel infinite scroll untuk pakai island append --}}
- <div x-intersect.once="$wire.loadMore()">
+ <div x-intersect.once="$wire.$island('products', { mode: 'append' }).loadMore()">
```

> **Constraint penting:** `@island` TIDAK BISA dipakai di dalam `@foreach` atau `@if`.
> Loop/conditional harus berada DI DALAM island, bukan sebaliknya.
> Rencana di atas sudah mengikuti constraint ini.

---

## Phase 3 — `@placeholder` : Native Livewire Skeleton
**Effort:** Rendah (±15 menit) | **Impact:** Code Quality & Readability

### Konteks
Skeleton loading saat ini menggunakan pattern manual yang cukup verbose:
```blade
<div wire:loading.class.remove="hidden" wire:target="setCategory" class="hidden">
    <!-- skeleton HTML panjang -->
</div>
<div wire:loading.remove wire:target="setCategory">
    <!-- konten asli -->
</div>
```

Livewire 4 memiliki cara native yang lebih deklaratif dengan `@placeholder`.

### File yang Diubah

#### [MODIFY] product-list.blade.php
```diff
+ @placeholder
+     <main class="max-w-xl mx-auto px-5 mt-4 grid grid-cols-2 gap-3">
+         @for ($s = 0; $s < 6; $s++)
+             {{-- skeleton cards (konten yang sama, dipindah ke sini) --}}
+         @endfor
+     </main>
+ @endplaceholder

- <div wire:loading.class.remove="hidden" wire:target="setCategory,lazy" class="hidden">
-     <main ...>...</main>
- </div>

- <div wire:loading.remove wire:target="setCategory">
      @island(name: 'products')
          ...
      @endisland
- </div>
```

> **Catatan:** `@placeholder` hanya untuk initial load (saat komponen pertama di-mount).
> Skeleton untuk `loadMore()` tetap menggunakan `wire:loading` karena itu adalah
> operasi partial, bukan initial render.

---

## Phase 4 — `wire:navigate.hover` : Prefetch Halaman Detail Produk
**Effort:** Sangat Rendah (±2 menit) | **Impact:** UX Feel (Speed)

### Konteks
Saat user hover di atas card produk, Livewire akan mulai men-download halaman
detail produk di background (setelah 60ms). Saat user klik, halaman muncul lebih
cepat karena sebagian atau seluruh HTML sudah di-cache.

Modifier `.hover` sudah mungkin ada, tapi perlu dipastikan konsisten di semua link.

### File yang Diubah

#### [MODIFY] product-list.blade.php
```diff
- <a href="{{ route('product.show', new Product($item)) }}" wire:navigate
+ <a href="{{ route('product.show', new Product($item)) }}" wire:navigate.hover
     class="absolute inset-0 z-10"></a>
```

---

## Ringkasan Phase

| # | Phase | Feature | Effort | Impact |
|---|---|---|---|---|
| 1 | URL Persistence | `#[Url]` + `#[Session]` | Rendah | ⭐⭐⭐⭐⭐ Shareable links |
| 2 | Append Islands | `@island.append` | Sedang | ⭐⭐⭐⭐⭐ Performa network |
| 3 | Native Placeholder | `@placeholder` | Rendah | ⭐⭐ Code quality |
| 4 | Prefetch Navigation | `wire:navigate.hover` | Sangat Rendah | ⭐⭐⭐ UX speed |

## Verification Plan

Setelah setiap phase, lakukan manual verification:

| Phase | Cara Verifikasi |
|---|---|
| 1 | Filter kategori → copy URL → buka di tab baru → filter harus sama |
| 1 | Pilih grid/list → refresh → mode tampilan harus bertahan |
| 2 | Buka Network tab browser → scroll → response loadMore harus 10 item saja, bukan akumulatif |
| 3 | Refresh halaman → skeleton muncul sebelum konten |
| 4 | Hover card produk → Network tab → ada request prefetch masuk setelah 60ms |
