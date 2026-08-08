# Livewire 4 Patterns & Architecture Decisions

Dokumen ini berisi keputusan arsitektur dan pola penulisan (patterns) yang terbukti bekerja dengan baik di *codebase* ini. Pola-pola ini harus diikuti untuk fitur-fitur baru demi menjaga konsistensi dan performa.

---

## 1. Zero-Roundtrip UI Policy

**Masalah:** Menggunakan `wire:model.live` di setiap form input atau toggle menyebabkan network request yang tidak perlu.
**Pola:** Semua interaksi form sebisa mungkin dilakukan di client-side (Alpine) dan hanya sinkronisasi ke server saat user menekan "Save".

**Aturan:**
- **JANGAN** gunakan `wire:model.live` untuk toggle (checkbox/switch) yang hanya digunakan untuk mengubah tampilan UI (misalnya memunculkan/menyembunyikan field lain).
- **GUNAKAN** `wire:model` biasa, ditambah dengan Alpine `$wire` untuk interaksi:

```blade
{{-- ❌ SALAH (Memicu server request tiap diklik) --}}
<input type="checkbox" wire:model.live="show_tax">
<div x-show="$wire.show_tax">...</div>

{{-- ✅ BENAR (Hanya mutasi state JS di client) --}}
<input type="checkbox" x-model="$wire.show_tax" wire:model="show_tax">
<div x-show="$wire.show_tax">...</div>
```

---

## 2. Dirty Indicators (Unsaved Changes)

**Masalah:** Saat mengedit form panjang (seperti halaman setting), user butuh indikator visual bahwa ada perubahan yang belum disimpan.
**Pola:** Gunakan fungsi `$dirty()` bawaan Livewire 4. Jangan membuat flag Alpine manual seperti `x-data="{ dirty: false }"`.

**Implementasi:**

```blade
{{-- Cek apakah SEMUA form di halaman ini berubah dari aslinya --}}
<button wire:click="save"
        class="btn transition-all"
        :class="$wire.$dirty() ? 'ring-2 ring-orange-500' : ''">
    Simpan Perubahan
</button>

{{-- Pesan opsional --}}
<p wire:show="$dirty">Ada perubahan yang belum disimpan.</p>
```

Livewire melacak `dirty` state di *client-side*. Ini tidak memerlukan `wire:model.live`. `$dirty` akan otomatis kembali `false` setelah metode `save()` dieksekusi dan state dari server kembali sinkron dengan browser.

---

## 3. Caching Eloquent Models (Anti __PHP_Incomplete_Class)

**Masalah:** Menyimpan objek utuh dari Eloquent model (`Model::first()`) ke `Cache` menggunakan driver selain `array` akan rusak di Laravel 11/13 karena adanya *security guard* `serializable_classes => false` di `config/cache.php`.
**Pola:** Jangan pernah menyimpan instance class object PHP ke cache. Selalu simpan tipe data primitif atau *array*.

**Implementasi Caching:**

```php
// ❌ SALAH (Akan memunculkan __PHP_Incomplete_Class TypeError)
public static function cached()
{
    return Cache::rememberForever('key', fn () => self::first());
}

// ✅ BENAR (Menyimpan array, reconstruct model setelah load)
public static function cached(): ?self
{
    $attrs = Cache::rememberForever(
        'store_setting_' . tenant('id'),
        fn () => self::first()?->getAttributes()
    );

    return $attrs ? (new self)->forceFill($attrs)->syncOriginal() : null;
}
```

*Note: `forceFill` + `syncOriginal` membuat model yang baru diinstansiasi menjadi "fresh", sama seperti aslinya, sehingga isDirty() tidak bernilai true sembarangan dan mutator tetap berjalan semestinya.*

---

## 4. Conditional Disabled / Gray-out Form Elements

**Masalah:** Saat sebuah fitur dimatikan (misal: Set "Toko Libur" → Input jam operasional di-disable), tampilan form harus menyesuaikan secara instan.
**Pola:** Gunakan Alpine `x-bind:disabled` dan `x-bind:class` yang membaca state dari `$wire`.

```blade
{{-- Toggle state --}}
<input type="checkbox" x-model="$wire.is_closed">

{{-- Element bergantung --}}
<input type="time"
       wire:model="open_time"
       x-bind:disabled="$wire.is_closed"
       x-bind:class="{ 'opacity-50 cursor-not-allowed bg-slate-100': $wire.is_closed }">
```

---

## 5. Form Data ke Service via DTO

**Masalah:** Livewire component mengirim data mentah (array) ke Service layer, melanggar layer isolation.

**Pola:** Setiap data dari Livewire ke Service HARUS melalui Data Transfer Object (`Spatie\LaravelData\Data`).

```php
// ✅ BENAR — DTO sebagai kontrak antar layer
$dto = ProductFormData::from($this->all());
$this->productService()->saveFromForm($existingProduct, $dto);

// ❌ SALAH — kirim array mentah
$this->productService()->saveFromForm($existingProduct, $this->all());
```

**Alasan:**
- Service tidak tahu asal data (Livewire / API / CLI).
- DTO memberikan type safety dan dokumentasi eksplisit.
- Validasi bisa dilakukan di DTO level.

---

## 6. Validation — `rules()` method over `#[Validate]`

**Pola:** Gunakan method `rules()` yang return array, bukan attribute `#[Validate]`.

```php
// ✅ BENAR — rules() method
protected function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'categoryId' => ['required', 'exists:categories,id'],
    ];
}

// ❌ SALAH — #[Validate] attribute (kurang eksplisit untuk form kompleks)
#[Validate(['name' => 'required|string|max:255'])]
public string $name = '';
```

**Alasan:** `rules()` memudahkan conditional rules (berubah berdasarkan state komponen lain) dan lebih mudah dibaca untuk form dengan banyak field.

---

## 7. Coding Standards (Single-line If, DB Transaction)

Lihat [Laravel 13 PATTERNS.md](../laravel13/PATTERNS.md) section 7 dan 8 untuk:
- **DB Transaction Pattern** — pakai `try/catch` + `beginTransaction`, bukan closure.
- **Single-line If** — satu baris eksekusi tanpa kurung kurawal.

Kedua aturan ini berlaku juga untuk semua file Livewire component.

---

## 8. Naming Computed Properties for Loops in Blade

**Masalah:** Menggunakan nama `slots()` sebagai fungsi/metode Computed Property di Livewire akan bentrok dengan *reserved keyword* Blade (`$slot` / `<x-slot>`). Hal ini menyebabkan property diproses dengan aneh atau dikonversi menjadi array kosong/null saat dirender di file `.blade.php`.

**Pola:** Jangan gunakan keyword bawaan view seperti `slots` atau `attributes` sebagai nama fungsi `#[Computed]`.

**Implementasi:**

```php
// ❌ SALAH (Akan bentrok dengan Blade reserved keyword)
#[Computed]
public function slots()
{
    return DeliverySlot::get();
}

// ✅ BENAR (Gunakan nama yang deskriptif dan spesifik)
#[Computed]
public function deliverySlots()
{
    return DeliverySlot::get();
}
```

---

## 9. Asset URL Tenant di Blade

**JANGAN PERNAH** hardcode path asset tenant menggunakan format `/tenant_{{ tenant('id') }}/...`. Selalu gunakan `Storage::url()`.

```blade
{{-- ❌ SALAH --}}
<img src="/tenant_{{ tenant('id') }}/{{ $logo }}">

{{-- ✅ BENAR --}}
<img src="{{ Storage::url($logo) }}">
```

Lihat detail di [Laravel 13 PATTERNS.md](../laravel13/PATTERNS.md) section 12.

---

## Referensi

- [STANDARDS.md](./STANDARDS.md) — Aturan nama class, MFC Alpine timing
- [Livewire 4 Forms & Validation](../livewire4/essentials/forms.md)
- [Livewire 4 Wire Dirty](../livewire4/html-directives/wire-dirty.md)
